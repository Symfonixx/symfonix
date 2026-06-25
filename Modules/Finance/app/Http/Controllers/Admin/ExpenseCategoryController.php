<?php

namespace Modules\Finance\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Finance\Actions\ExpenseCategory\CreateExpenseCategoryAction;
use Modules\Finance\Actions\ExpenseCategory\DeleteExpenseCategoryAction;
use Modules\Finance\Actions\ExpenseCategory\ListExpenseCategoriesAction;
use Modules\Finance\Actions\ExpenseCategory\UpdateExpenseCategoryAction;
use Modules\Finance\DTOs\ExpenseCategory\ExpenseCategoryData;
use Modules\Finance\Http\Requests\StoreExpenseCategoryRequest;
use Modules\Finance\Http\Requests\UpdateExpenseCategoryRequest;
use Modules\Finance\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        private readonly ListExpenseCategoriesAction $listExpenseCategoriesAction,
        private readonly CreateExpenseCategoryAction $createExpenseCategoryAction,
        private readonly UpdateExpenseCategoryAction $updateExpenseCategoryAction,
        private readonly DeleteExpenseCategoryAction $deleteExpenseCategoryAction,
    ) {
        $this->authorizeResource(ExpenseCategory::class, 'expense_category');
        $this->setActive('finance_expense_categories');
    }

    public function index(): View
    {
        $categories = $this->listExpenseCategoriesAction->execute();

        return view('finance::admin.expense_category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('finance::admin.expense_category.create');
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $data = ExpenseCategoryData::fromRequest($request->validated());
        $this->createExpenseCategoryAction->execute($data);

        return redirect()->route('admin.finance.expense-categories.index');
    }

    public function edit(ExpenseCategory $expense_category): View
    {
        return view('finance::admin.expense_category.edit', ['category' => $expense_category]);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expense_category): RedirectResponse
    {
        $data = ExpenseCategoryData::fromRequest($request->validated());
        $this->updateExpenseCategoryAction->execute($expense_category, $data);

        return redirect()->route('admin.finance.expense-categories.index');
    }

    public function destroy(ExpenseCategory $expense_category): RedirectResponse
    {
        $this->deleteExpenseCategoryAction->execute($expense_category);

        return redirect()->route('admin.finance.expense-categories.index');
    }
}
