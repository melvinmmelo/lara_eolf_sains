<?php

namespace App\Http\Controllers;

use App\Models\Expenses;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $branchCode = session('branch_code');

        $query = $this->filteredQuery($request)->with('creator');

        $expenses = $query->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $total = (clone $query)->sum('amount');

        $payees = Expenses::branch($branchCode)
            ->whereNotNull('payee')
            ->where('payee', '!=', '')
            ->distinct()
            ->orderBy('payee')
            ->pluck('payee');

        return view('expenses.index', [
            'expenses' => $expenses,
            'total' => $total,
            'payees' => $payees,
            'categories' => Expenses::CATEGORIES,
            'paymentMethods' => Expenses::PAYMENT_METHODS,
        ]);
    }

    public function export(Request $request)
    {
        $expenses = $this->filteredQuery($request)
            ->with('creator')
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expenses');

        $headers = ['Date', 'Category', 'Particulars', 'Payee', 'Amount', 'Payment Method', 'Ref No.', 'Remarks', 'Recorded By'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($expenses as $expense) {
            $sheet->setCellValue('A' . $row, optional($expense->expense_date)->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $expense->category);
            $sheet->setCellValue('C' . $row, $expense->particulars);
            $sheet->setCellValue('D' . $row, $expense->payee);
            $sheet->setCellValueExplicit('E' . $row, $expense->amount, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValue('F' . $row, $expense->payment_method);
            $sheet->setCellValue('G' . $row, $expense->reference_no);
            $sheet->setCellValue('H' . $row, $expense->remarks);
            $sheet->setCellValue('I' . $row, optional($expense->creator)->fullName);
            $row++;
        }

        $sheet->getStyle('E2:E' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Total row
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'Total');
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->setCellValueExplicit('E' . $totalRow, $expenses->sum('amount'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
        $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
            ],
        ]);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $branch = session('branch_code');
        $filename = 'expenses_' . $branch . '_' . now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function store(Request $request)
    {
        $data = $this->validateExpense($request);

        $data['branch_code'] = session('branch_code');
        $data['created_by'] = auth()->id();

        Expenses::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully!');
    }

    public function edit($id)
    {
        $expense = Expenses::branch(session('branch_code'))->findOrFail($id);

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expenses::branch(session('branch_code'))->findOrFail($id);

        $expense->update($this->validateExpense($request));

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy($id)
    {
        $expense = Expenses::branch(session('branch_code'))->findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }

    private function filteredQuery(Request $request)
    {
        $query = Expenses::branch(session('branch_code'));

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('particulars', 'like', "%{$search}%")
                    ->orWhere('payee', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:191',
            'particulars' => 'required|string|max:191',
            'payee' => 'nullable|string|max:191',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:191',
            'reference_no' => 'nullable|string|max:191',
            'remarks' => 'nullable|string',
        ]);
    }
}
