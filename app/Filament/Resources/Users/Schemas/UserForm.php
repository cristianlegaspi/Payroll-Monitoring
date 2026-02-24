<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Details')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        Select::make('branch_name')
                    ->options([
                        'San Isidro' => 'San Isidro',
                        'Dita' => 'Dita',
                        'Sta Cruz' => 'Sta Cruz',
                        'Tagapo' => 'Tagapo',
                    ])
                    ->searchable()
                    ->required()
                    ->placeholder('Select a Branch'),
                    ])->columns(2),

                
                Section::make('User Credentials')
                    ->schema([
                        TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                              // DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),

                 Select::make('role')
                    ->options([
                        'staff' => 'staff',
                        'admin' => 'admin',
                        'super-admin' => 'super-admin',
                    ])
                    ->required()
                    ->searchable()
                    ->placeholder('Select a Role')

                
                    ])->columns(3),




           
          

                
            ])->columns(1);
    }
}
