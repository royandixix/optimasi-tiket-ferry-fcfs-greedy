<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Auth\Responses\RoleLoginResponse;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getRoleFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
        ]);
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->label('Masuk sebagai')
            ->placeholder('Pilih role akun')
            ->options([
                User::ROLE_ADMIN => 'Admin',
                User::ROLE_PETUGAS => 'Petugas Validasi Tiket',
            ])
            ->native(false)
            ->required()
            ->helperText('Dashboard Filament hanya untuk Admin dan Petugas Validasi.');
    }

    protected function getCredentialsFromFormData(
        #[SensitiveParameter] array $data
    ): array {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'status' => 'aktif',
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        return $response === null
            ? null
            : new RoleLoginResponse();
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Masuk ke Sistem Optimasi Tiket Ferry';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Khusus Admin dan Petugas Validasi Tiket.';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk ke Dashboard');
    }
}
