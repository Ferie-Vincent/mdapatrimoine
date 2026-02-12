<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\DepositRefund;
use App\Models\Lease;
use App\Models\LeaseMonthly;
use App\Models\Payment;
use App\Services\AuditService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $sciId = $request->attributes->get('sci_id');

        $query = Payment::query()->with(['leaseMonthly.lease.property', 'leaseMonthly.lease.tenant']);

        if ($sciId) {
            $query->where('sci_id', $sciId);
        }

        if ($month = $request->input('month')) {
            $query->whereHas('leaseMonthly', function ($q) use ($month) {
                $q->where('month', $month);
            });
        }

        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('leaseMonthly.lease.tenant', function ($tq) use ($search) {
                      $tq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderByDesc('paid_at')
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(LeaseMonthly $monthly): RedirectResponse
    {
        return redirect()->route('monthlies.show', $monthly);
    }

    public function store(StorePaymentRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Payment::class);

        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/payments', 'public');
        }
        unset($data['receipt']);

        $monthly = LeaseMonthly::findOrFail($data['lease_monthly_id']);

        $payment = $this->paymentService->recordPayment($monthly, $data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Paiement enregistre avec succes.']);
        }

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['leaseMonthly.lease.property', 'leaseMonthly.lease.tenant', 'recorder']);

        return view('payments.show', compact('payment'));
    }

    public function refundDeposit(Request $request): RedirectResponse
    {
        $this->authorize('create', Payment::class);

        $data = $request->validate([
            'lease_id'    => ['required', 'exists:leases,id'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'refunded_at' => ['required', 'date'],
            'method'      => ['required', 'in:especes,virement,cheque,mobile_money,versement_especes,depot_bancaire'],
            'reference'   => ['nullable', 'string', 'max:255'],
            'note'        => ['nullable', 'string'],
            'receipt'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts/deposit-refunds', 'public');
        }

        $error = DB::transaction(function () use ($data) {
            // Lock the lease inside the transaction to prevent concurrent refunds
            $lease = Lease::lockForUpdate()->findOrFail($data['lease_id']);

            $depositAmount = (float) ($lease->deposit_amount ?? 0);
            $alreadyReturned = (float) ($lease->deposit_returned_amount ?? 0);
            $remaining = $depositAmount - $alreadyReturned;

            if ((float) $data['amount'] > $remaining) {
                return 'Le montant du remboursement (' . number_format((float) $data['amount'], 0, ',', ' ') .
                    ' FCFA) dépasse le restant de la caution (' . number_format($remaining, 0, ',', ' ') . ' FCFA).';
            }

            $refund = DepositRefund::create([
                'lease_id'     => $lease->id,
                'sci_id'       => $lease->sci_id,
                'amount'       => $data['amount'],
                'refunded_at'  => $data['refunded_at'],
                'method'       => $data['method'],
                'reference'    => $data['reference'] ?? null,
                'receipt_path' => $data['receipt_path'] ?? null,
                'note'         => $data['note'] ?? null,
                'recorded_by'  => auth()->id(),
            ]);

            $lease->update([
                'deposit_returned_amount' => $alreadyReturned + (float) $data['amount'],
            ]);

            AuditService::log('deposit_refund', $refund, [
                'lease_id' => $lease->id,
                'amount'   => $data['amount'],
                'method'   => $data['method'],
            ]);

            return null;
        });

        if ($error) {
            return redirect()->back()->withErrors(['refund_amount' => $error])->withInput();
        }

        return redirect()->back()->with('success', 'Remboursement de caution enregistré avec succès.');
    }
}
