<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class EmployeeOverview extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $query = Employee::query();

        // Role-based filtering logic
        if ($user->role === 'super-admin' || $user->role === 'admin') {
            // No filter applied - see all employees
            $label = 'Total Employees (All Branches)';
            $description = 'Global system count';
        } elseif ($user->role === 'staff') {
            // Filter by the branch_name string column
            $query->where('branch_name', $user->branch_name);
            
            $label = 'Branch Employees';
            $description = "Employees in {$user->branch_name}";
        } else {
            // Fallback for any other roles
            $query->where('id', 0); // Returns empty if role is unknown
            $label = 'Employees';
            $description = 'Access restricted';
        }

        return [
            Stat::make($label, $query->count())
                ->description($description)
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}