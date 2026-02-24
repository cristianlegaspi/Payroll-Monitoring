<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeOverview extends StatsOverviewWidget
{

   protected function getColumns(): int
    {
        return 3; // Max 3 stats per row
    }

    protected function getStats(): array
    {
        return [

             Stat::make('Total Employees', Employee::count())
                ->description('All registered employees')
                ->icon('heroicon-o-users')
                ->color('primary'),
            //
        ];
    }
}
