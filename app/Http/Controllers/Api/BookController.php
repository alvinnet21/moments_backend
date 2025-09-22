<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Http\Requests\BookAvailabilityRequest;
use App\Http\Requests\BookAdminOffRequest;
use App\Services\BookService;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookController extends ApiController
{
    public function __construct(private readonly BookService $service)
    {
    }

    public function index()
    {
        $perPage = 20;

        // Accept optional query params: date_from, date_to (unix seconds or YYYY-MM-DD)
        $request = request();
        $dateFromParam = $request->query('date_from');
        $dateToParam = $request->query('date_to');

        // Defaults: start and end of current month
        $start = now()->startOfMonth()->startOfDay();
        $end = now()->endOfMonth()->startOfDay();

        if ($dateFromParam !== null) {
            $start = is_numeric($dateFromParam)
                ? now()->setTimestamp((int) $dateFromParam)->startOfDay()
                : \Illuminate\Support\Carbon::parse($dateFromParam)->startOfDay();
        }
        if ($dateToParam !== null) {
            $end = is_numeric($dateToParam)
                ? now()->setTimestamp((int) $dateToParam)->startOfDay()
                : \Illuminate\Support\Carbon::parse($dateToParam)->startOfDay();
        }

        $list = $this->service->list($perPage, $start->getTimestamp(), $end->getTimestamp());
        return $this->paginated($list);
    }

    public function show(int $id)
    {
        try {
            $book = $this->service->findOrFail($id);
            return $this->success($book);
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        }
    }

    public function store(BookStoreRequest $request)
    {
        try {
            $book = $this->service->create($request->validated());
            // Kirim email konfirmasi ke customer; laporkan status di message response
            $emailMsg = null;
            try {
                if (!empty($book->customer_email)) {
                    $toName = $book->customer_name ?: null;
                    $mailable = (new BookingConfirmation($book))
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->withSymfonyMessage(function ($sym) {
                            // Mark as important/high priority
                            if (method_exists($sym, 'priority')) {
                                $sym->priority(1);
                            }
                            if (method_exists($sym, 'getHeaders')) {
                                $sym->getHeaders()->addTextHeader('Importance', 'High');
                                $sym->getHeaders()->addTextHeader('X-MSMail-Priority', 'High');
                            }
                        });
                    Mail::to($book->customer_email, $toName)->send($mailable);
                    $emailMsg = 'Email sent to customer.';
                } else {
                    $emailMsg = 'No customer email provided; email skipped.';
                }
            } catch (\Throwable $e) {
                $emailMsg = 'Email failed: ' . $e->getMessage();
            }
            return $this->success($book, $emailMsg, 201);
        } catch (\LogicException $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function update(int $id, BookUpdateRequest $request)
    {
        try {
            $book = $this->service->update($id, $request->validated());
            return $this->success($book);
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Book is already in the process') {
                return $this->error($e->getMessage(), 422);
            }
            throw $e;
        } catch (\LogicException $e) {
            return $this->error($e->getMessage(), 409);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return response()->noContent();
        } catch (ModelNotFoundException) {
            return $this->error('Data not found', 404);
        }
    }

    public function availability(BookAvailabilityRequest $request)
    {
        $data = $request->validated();
        $list = $this->service->availabilityRange(
            (int) $data['employee_id'],
            now()->format('Y-m-d'),
            180,
            null
        );
        return $this->success($list);
    }

    public function adminOff(BookAdminOffRequest $request)
    {
        try {
            $payload = $request->validated();
            // Create a FINISHED booking with dummy customer info
            $book = $this->service->createAdminOff($payload);
            return $this->success($book, null, 201);
        } catch (\LogicException $e) {
            return $this->error($e->getMessage(), 409);
        }
    }
}
