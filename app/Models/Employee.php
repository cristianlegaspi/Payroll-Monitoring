<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'employee_number',
        'full_name',
        'position',
        'branch_name',
        'employment_status',
        'daily_rate',
        'date_hired',
        'employee_type',
        'tin',
        'sss_ee',
        'sss_er',
        'sss_salary_loan',
        'sss_calamity_loan',
        'philhealth_ee',
        'philhealth_er',
        'pagibig_ee',
        'pagibig_er',
        'pagibig_loan',
        'status',
        'date_of_birth',
        'premium_voluntary_ss_contribution',
        'pagibig_salary_loan'

    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'date_hired' => 'date',
        'date_of_birth' => 'date',
    ];

    public function getDisplayNameAttribute()
    {
        return "{$this->employee_number} - {$this->full_name}";
    }

   

    
    public function dtrs()
    {
        return $this->hasMany(DailyTimeRecord::class);
    }
}
