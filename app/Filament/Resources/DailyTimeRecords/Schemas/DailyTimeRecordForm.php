<?php

namespace App\Filament\Resources\DailyTimeRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Section;
use Carbon\Carbon;
use App\Models\Employee;

class DailyTimeRecordForm
{
    public static function configure($schema)
    {
        $user = Filament::auth()->user();

        return $schema
            ->components([
                Section::make('Employee & Date')
                    ->schema([
                        // Branch dropdown
                        Select::make('branch_name')
                            ->label('Branch')
                            ->options(function () use ($user) {
                                // Admin / Super Admin sees all branches
                                if (in_array($user->role, ['admin', 'super-admin'])) {
                                    return Employee::distinct()->pluck('branch_name', 'branch_name');
                                }

                                // Staff sees only their branch
                                return [$user->branch_name => $user->branch_name];
                            })
                            ->default($user?->branch_name)
                            ->disabled(fn () => $user->role === 'staff') // staff cannot change branch
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('employee_id', null))
                            ->required(),

                        // Employee dropdown
                        Select::make('employee_id')
                            ->relationship(
                                name: 'employee',
                                titleAttribute: 'full_name',
                                modifyQueryUsing: function ($query, $get) use ($user) {
                                    // Admin / Super Admin: filter by selected branch
                                    if (in_array($user->role, ['admin', 'super-admin'])) {
                                        if ($get('branch_name')) {
                                            return $query->where('branch_name', $get('branch_name'));
                                        }
                                        return $query;
                                    }

                                    // Staff: always filter by their branch
                                    return $query->where('branch_name', $user->branch_name);
                                }
                            )
                            ->label('Employee')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('work_date')
                            ->default(now())
                            ->required(),
                    ])->columns(3),

                // Attendance Status
                Section::make('Attendance Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'On duty' => 'On duty',
                                'Rest day' => 'Rest day',
                                'Absent With Pay' => 'Absent With Pay',
                                'Absent Without Pay' => 'Absent Without Pay',
                                'Legal Holiday' => 'Legal Holiday',
                                'Special Holiday' => 'Special Holiday',
                            ])
                            ->default('On duty')
                            ->live()
                            ->afterStateUpdated(fn($get, $set) => self::compute($get, $set))
                            ->required(),

                        TextInput::make('remarks')
                            ->label('System Remarks')
                            ->readOnly()
                            ->extraAttributes(['class' => 'font-bold text-primary-600']),
                    ])->columns(2),

                // Biometrics Details
                Section::make('Biometrics Details')
                    ->schema([
                        Section::make('1st Shift')
                            ->schema([
                                TimePicker::make("shift1_time_in")->label("Time In")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                                TimePicker::make("shift1_time_out")->label("Time Out")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                            ])->columns(2),

                        Section::make('2nd Shift')
                            ->schema([
                                TimePicker::make("shift2_time_in")->label("Time In")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                                TimePicker::make("shift2_time_out")->label("Time Out")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                            ])->columns(2),

                        Section::make('3rd Shift')
                            ->schema([
                                TimePicker::make("shift3_time_in")->label("Time In")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                                TimePicker::make("shift3_time_out")->label("Time Out")->seconds(true)->live()->afterStateUpdated(fn($get, $set) => self::compute($get, $set)),
                            ])->columns(2),
                    ])->columns(3),

                // Totals
                Section::make('Totals')
                    ->schema([
                        TextInput::make('total_hours')
                            ->label('Regular Hours (Payable)')
                            ->numeric()
                            ->readOnly()
                            ->default(0),

                        TextInput::make('overtime_hours')
                            ->label('Overtime Hours')
                            ->numeric()
                            ->readOnly()
                            ->default(0),

                        TextInput::make('undertime_hours')
                            ->label('Undertime Hours')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                    ])->columns(3),
            ])->columns(1);
    }

    // Compute logic (same as before)
    protected static function compute($get, $set)
    {
        $status = $get('status');
        $totalMinutesWorked = 0;
        $isNightShift = false;

        for ($i = 1; $i <= 3; $i++) {
            $timeIn = $get("shift{$i}_time_in");
            $timeOut = $get("shift{$i}_time_out");

            if (!$timeIn || !$timeOut) continue;

            $in = Carbon::parse($timeIn);
            $out = Carbon::parse($timeOut);

            if ($out <= $in) {
                $out->addDay();
            }

            $hourIn = (int)$in->format('H');
            if ($hourIn >= 22 || $hourIn < 6) {
                $isNightShift = true;
            }

            $totalMinutesWorked += $in->diffInMinutes($out);
        }

        $actualWorkedHours = round($totalMinutesWorked / 60, 2);
        $finalTotal = 0;
        $finalOT = 0;
        $finalUndertime = 0;

        switch ($status) {
            case 'Absent With Pay':
                $finalTotal = 8.00;
                break;

            case 'Legal Holiday':
                $finalTotal = 8.00;
                $finalOT = $actualWorkedHours;
                break;

            case 'Rest day':
            case 'Special Holiday':
                $finalTotal = 0;
                $finalOT = $actualWorkedHours;
                break;

            case 'On duty':
            default:
                if ($actualWorkedHours >= 8) {
                    $finalTotal = 8.00;
                    $finalOT = round($actualWorkedHours - 8, 2);
                } else {
                    $finalTotal = $actualWorkedHours;
                    $finalUndertime = ($actualWorkedHours > 0) ? round(8 - $actualWorkedHours, 2) : 0;
                }
                break;
        }

        $set('total_hours', $finalTotal);
        $set('overtime_hours', $finalOT);
        $set('undertime_hours', $finalUndertime);

        if (in_array($status, ['Rest day', 'Absent With Pay', 'Legal Holiday', 'Special Holiday'])) {
            $set('remarks', $status);
        } elseif ($status === 'On duty') {
            if ($actualWorkedHours == 0) {
                $set('remarks', 'Absent Without Pay');
                $set('undertime_hours', 0);
            } elseif ($actualWorkedHours < 8) {
                $set('remarks', 'Undertime');
            } else {
                $set('remarks', $isNightShift ? 'Night Shift On duty' : 'On duty');
            }
        }
    }
}