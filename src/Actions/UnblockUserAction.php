<?php

namespace Idez\NovaSecurity\Actions;

use Idez\NovaSecurity\Exceptions\OneTimePasswordException;
use function Idez\NovaSecurity\Helpers\checkOtp;
use function Idez\NovaSecurity\Helpers\verifyOTP;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UnblockUserAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $name = "Desbloquear Usuário";

    public function name(): string
    {
        $this->name = __('nova-security::actions.unblock_user');

        return parent::name();
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return array|string[]|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws OneTimePasswordException
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() > 1) {
            return Action::danger(trans('nova-security::actions.this_operation_cannot_be_performed_in_bulk'));
        }

        if (! checkOtp()) {
            return Action::danger('Código de segurança inválido.');
        }

        $user = $models->first();

        $user->blocked_at = false;
        $user->save();
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Text::make('One time password', 'otp')
                ->rules('required', 'numeric', 'digits:6')
                ->canSee(fn () => verifyOTP()),
        ];
    }
}
