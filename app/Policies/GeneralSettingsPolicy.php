<?php
namespace App\Policies;

use App\Models\User;
use Joaopaulolndev\FilamentGeneralSettings\Models\GeneralSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralSettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user) : bool
    {
        // Puedes verificar si el usuario tiene el permiso para ver cualquier configuración general
        return $user->can('page_GeneralSettingsPage');
    }

    /**
     * Determina si el usuario puede ver el modelo.
     */
    public function view(User $user, GeneralSetting $generalSetting) : bool
    {
        // Verifica si el usuario tiene permiso para ver esta configuración en particular
        return $user->can('page_GeneralSettingsPage');
    }

    /**
     * Determina si el usuario puede actualizar el modelo.
     */
    public function update(User $user, GeneralSetting $generalSetting) : bool
    {
        // Puedes verificar si el usuario tiene el permiso de editar la configuración
        return $user->can('page_EditProfilePage');
    }

    // Si necesitas más métodos, agréguelos aquí, como delete, create, etc.
}
