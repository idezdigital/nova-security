<?php

namespace Idez\NovaSecurity\Actions;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FALaravel\Google2FA;
use PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException;
use PragmaRX\Google2FAQRCode\QRCode\Bacon;

class SetupUserTwoFactorAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public function name(): string
    {
        $this->name = __('nova-security::actions.setup_two_factor');

        return parent::name();
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return string[]
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        if ($models->count() > 1) {
            return Action::danger(trans('nova-security::actions.this_operation_cannot_be_performed_in_bulk'));
        }

        $google2fa = app(Google2FA::class);
        $user = $models->first();

        $secret = decrypt($fields->get('token'));
        $otp = $fields->get('otp');


        if ($google2fa->verifyGoogle2FA($secret, $otp)) {
            $column = config('google2fa.otp_secret_column');

            $user->{$column} = $secret;
            $user->save();

            $google2fa->logout();

            return Action::redirect(config('nova.path'));
        }


        return Action::danger(trans('nova-security::actions.invalid_code'));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     * @throws MissingQrCodeServiceException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function fields()
    {
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey(32);

        // @phpstan-ignore-next-line
        $holder = auth()->user()->name ?? auth()->user()?->getAuthIdentifier();

        $qrCode = $google2fa
            ->setQrcodeService(
                new Bacon(
                    new SvgImageBackEnd()
                )
            )
            ->getQRCodeInline(
                company: config('app.name'),
                holder: $holder,
                secret: $secret
            );

        $link = $google2fa->getQRCodeUrl(
            company: config('app.name'),
            holder: $holder,
            secret: $secret
        );


        return [
            Heading::make('<div class="flex justify-center">' . $qrCode . '</div>')
                ->asHtml(),
            Text::make('Link', 'link')->default($link)->readonly(),
            Text::make('One time password', 'otp'),
            Hidden::make('Secret', 'token')->default(encrypt($secret)),
        ];
    }

    private function twoFactorActionStillValid(): Closure
    {
        /**
         * @param NovaRequest $request
         * @return bool
         */
        return fn (NovaRequest $request): bool => $request instanceof ActionRequest || ($request->resource()->id === auth()->user()?->getAuthIdentifier() && ! filled($request->resource()->two_factor_secret));
    }

    public function canSee(?Closure $callback = null): self
    {
        $callback ??= $this->twoFactorActionStillValid();

        return parent::canSee($callback);
    }

    public function canRun(?Closure $callback = null): self
    {
        $callback ??= $this->twoFactorActionStillValid();

        return parent::canRun($callback);
    }

    public function onlyOnDetail($value = true): self
    {
        return parent::onlyOnDetail($value);
    }
}
