<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Exception;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        $customError = $this->handleCustomException($exception);

        return response()->view('errors.custom', [
            'code' => $customError['code'],
            'message' => $customError['message']
        ], $customError['status']);
    }

    protected function handleCustomException(Exception $exception)
    {
        // Define your custom error codes and messages
        $errorMap = [
            'Illuminate\Database\Eloquent\ModelNotFoundException' => [
                'code' => 'SASQUATCH',
                'message' => 'The requested model was not found, contact the Administrator.',
                'status' => 404
            ],
            'Illuminate\Auth\AuthenticationException' => [
                'code' => 'SENTINEL',
                'message' => 'Authentication or Permission error, contact the Administrator.',
                'status' => 401
            ],
            'Symfony\Component\HttpKernel\Exception\HttpException' => [
                'code' => 'LIGHTHOUSE',
                'message' => 'Generic HTTP error, contact the Administrator.',
                'status' => 500
            ],
            'Illuminate\Auth\Access\AuthorizationException' => [
                'code' => 'GUARDIAN',
                'message' => 'Access to this resource is forbidden.',
                'status' => 403
            ],
            'Illuminate\Validation\ValidationException' => [
                'code' => 'ZEPHYR',
                'message' => 'The request could not be understood. Please check your input.',
                'status' => 400
            ],
            'Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException' => [
                'code' => 'OASIS',
                'message' => 'Service is currently unavailable. Please try again later.',
                'status' => 503
            ],
            'Illuminate\Http\Exceptions\HttpResponseException' => [
                'code' => 'ALCHEMY',
                'message' => 'The request was well-formed but could not be followed due to semantic errors.',
                'status' => 422
            ],
            'Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException' => [
                'code' => 'STAMPEDE',
                'message' => 'Too many requests. Please slow down.',
                'status' => 429
            ],
            'Symfony\Component\HttpKernel\Exception\RequestTimeoutHttpException' => [
                'code' => 'SNAIL',
                'message' => 'The request timed out. Please try again.',
                'status' => 408
            ],
            'Symfony\Component\HttpKernel\Exception.TeapotHttpException' => [
                'code' => 'TEAPOT',
                'message' => 'I\'m a teapot. This is not a valid request.',
                'status' => 418
            ],
            'Illuminate\Session\TokenMismatchException' => [
                'code' => 'LYNX',
                'message' => 'The page has expired due to inactivity. Please refresh and try again.',
                'status' => 419
            ],
            'Illuminate\Database\QueryException' => [
                'code' => 'VAULT',
                'message' => 'Database error, contact the Administrator.',
                'status' => 500
            ],
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => [
                'code' => 'LOST',
                'message' => 'The requested resource was not found.',
                'status' => 404
            ],
            'Symfony\Component\HttpKernel\Exception\TeapotHttpException' => [
                'code' => 'TEAPOT',
                'message' => 'I\'m a teapot. This is not a valid request.',
                'status' => 418
            ],
            'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException' => [
                'code' => 'GATEKEEPER',
                'message' => 'The request method is not allowed for this resource.',
                'status' => 405
            ],
            'Symfony\Component\HttpKernel\Exception\ConflictHttpException' => [
                'code' => 'HARMONY',
                'message' => 'A conflict occurred while processing the request.',
                'status' => 409
            ],
            'Symfony\Component\HttpKernel\Exception\GoneHttpException' => [
                'code' => 'WEASEL',
                'message' => 'The requested resource is no longer available.',
                'status' => 410
            ],
            'Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException' => [
                'code' => 'RULER',
                'message' => 'The request requires a content length header.',
                'status' => 411
            ],
            'Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException' => [
                'code' => 'SANCTUARY',
                'message' => 'The server does not meet one of the preconditions that the requester put on the request.',
                'status' => 412
            ],
            'Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException' => [
                'code' => 'SAGE',
                'message' => 'The server requires the request to be conditional.',
                'status' => 428
            ],
            'Spatie\Permission\Exceptions\UnauthorizedException' => [
                'code' => 'TELESCOPE',
                'message' => 'You do not have permission to access this resource.',
                'status' => 403
            ],
            'Spatie\Permission\Exceptions\RoleDoesNotExist' => [
                'code' => 'GHOST',
                'message' => 'The requested role does not exist.',
                'status' => 404
            ],
            'Spatie\Permission\Exceptions\RoleAlreadyExists' => [
                'code' => 'PHANTOM',
                'message' => 'The requested role already exists.',
                'status' => 409
            ],
            'Spatie\Permission\Exceptions\PermissionDoesNotExist' => [
                'code' => 'WRAITH',
                'message' => 'The requested permission does not exist.',
                'status' => 404
            ],
            'Spatie\Permission\Exceptions\PermissionAlreadyExists' => [
                'code' => 'SPECTER',
                'message' => 'The requested permission already exists.',
                'status' => 409
            ],
            'Spatie\Permission\Exceptions\GuardDoesNotMatch' => [
                'code' => 'SHADOW',
                'message' => 'The requested guard does not match the current guard.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\InvalidSignature' => [
                'code' => 'DRAGON',
                'message' => 'The email verification link is invalid.',
                'status' => 400
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailAlreadyVerified' => [
                'code' => 'PHOENIX',
                'message' => 'The email has already been verified.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationTimeout' => [
                'code' => 'WYVERN',
                'message' => 'The email verification link has expired.',
                'status' => 400
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationMismatch' => [
                'code' => 'GRIFFIN',
                'message' => 'The email verification link does not match the email.',
                'status' => 400
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationAlreadyCompleted' => [
                'code' => 'CHIMERA',
                'message' => 'The email verification has already been completed.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationAlreadyStarted' => [
                'code' => 'BASILISK',
                'message' => 'The email verification has already been started.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationNotStarted' => [
                'code' => 'KRAKEN',
                'message' => 'The email verification has not been started.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationNotPending' => [
                'code' => 'MINOTAUR',
                'message' => 'The email verification is not pending.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationAlreadyCancelled' => [
                'code' => 'CERBERUS',
                'message' => 'The email verification has already been cancelled.',
                'status' => 409
            ],
            'ProtoneMedia\LaravelVerifyNewEmail\Exceptions\EmailVerificationCancelled' => [
                'code' => 'HYDRA',
                'message' => 'The email verification has been cancelled.',
                'status' => 409
            ],
            'Laravel\Passport\Exceptions\MissingScopeException' => [
                'code' => 'SIREN',
                'message' => 'The request requires a scope that the token does not have.',
                'status' => 403
            ],
            'Laravel\Passport\Exceptions\OAuthServerException' => [
                'code' => 'PELICAN',
                'message' => 'OAuth server error, contact the Administrator.',
                'status' => 500
            ],
            'Laravel\Passport\Exceptions\InvalidClientException' => [
                'code' => 'OWL',
                'message' => 'The client is invalid.',
                'status' => 401
            ],
            'Laravel\Passport\Exceptions\InvalidGrantException' => [
                'code' => 'RAVEN',
                'message' => 'The grant is invalid.',
                'status' => 401
            ],
            'Laravel\Passport\Exceptions\InvalidRequestException' => [
                'code' => 'SWAN',
                'message' => 'The request is invalid.',
                'status' => 400
            ],
            'Laravel\Passport\Exceptions\InvalidScopeException' => [
                'code' => 'TITAN',
                'message' => 'The scope is invalid.',
                'status' => 400
            ],
            'Laravel\Passport\Exceptions\NoActiveAccessTokenException' => [
                'code' => 'UNICORN',
                'message' => 'No active access token.',
                'status' => 401
            ],
            'Laravel\Passport\Exceptions\NoUserException' => [
                'code' => 'YETI',
                'message' => 'No user found.',
                'status' => 401
            ],
            'Laravel\Passport\Exceptions\ScopeDeniedException' => [
                'code' => 'ZOMBIE',
                'message' => 'The scope is denied.',
                'status' => 403
            ],
            'Laravel\Passport\Exceptions\TokenMismatchException' => [
                'code' => 'COIN',
                'message' => 'The token does not match.',
                'status' => 400
            ],
            'Laravel\Passport\Exceptions\TokenNotFoundException' => [
                'code' => 'HARMONICA',
                'message' => 'The token was not found.',
                'status' => 404
            ],
            'Laravel\Passport\Exceptions\TokenRevokedException' => [
                'code' => 'JACKAL',
                'message' => 'The token has been revoked.',
                'status' => 401
            ],
            'Laravel\Passport\Exceptions\UnsupportedGrantTypeException' => [
                'code' => 'CHAMELEON',
                'message' => 'The grant type is unsupported.',
                'status' => 400
            ],
            'Laravel\Passport\Exceptions\UnsupportedResponseTypeException' => [
                'code' => 'DRAGONFLY',
                'message' => 'The response type is unsupported.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchQuery' => [
                'code' => 'SALAMANDER',
                'message' => 'The search query is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\EngineNotConfigured' => [
                'code' => 'TURTLE',
                'message' => 'The search engine is not configured.',
                'status' => 500
            ],
            'Laravel\Scout\Exceptions\InvalidModel' => [
                'code' => 'VIPER',
                'message' => 'The model is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\IndexNotFoundException' => [
                'code' => 'WASP',
                'message' => 'The index was not found.',
                'status' => 404
            ],
            'Laravel\Scout\Exceptions\InvalidDriver' => [
                'code' => 'XENON',
                'message' => 'The driver is invalid.',
                'status' => 500
            ],
            'Laravel\Scout\Exceptions\InvalidQuery' => [
                'code' => 'YAK',
                'message' => 'The query is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchable' => [
                'code' => 'ZEBRA',
                'message' => 'The searchable is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSettings' => [
                'code' => 'ALBATROSS',
                'message' => 'The settings are invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableModel' => [
                'code' => 'BISON',
                'message' => 'The searchable model is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableArray' => [
                'code' => 'CAMEL',
                'message' => 'The searchable array is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableFields' => [
                'code' => 'DODO',
                'message' => 'The searchable fields are invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableArrayFields' => [
                'code' => 'EAGLE',
                'message' => 'The searchable array fields are invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableArrayDriver' => [
                'code' => 'FALCON',
                'message' => 'The searchable array driver is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableArrayIndex' => [
                'code' => 'GIRAFFE',
                'message' => 'The searchable array index is invalid.',
                'status' => 400
            ],
            'Laravel\Scout\Exceptions\InvalidSearchableArraySettings' => [
                'code' => 'HAWK',
                'message' => 'The searchable array settings are invalid.',
                'status' => 400
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted' => [
                'code' => 'MERMAID',
                'message' => 'The media cannot be deleted.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeUpdated' => [
                'code' => 'NEPTUNE',
                'message' => 'The media cannot be updated.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAdded' => [
                'code' => 'OCEAN',
                'message' => 'The media cannot be added.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeCopied' => [
                'code' => 'PIRATE',
                'message' => 'The media cannot be copied.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeMoved' => [
                'code' => 'QUARTERDECK',
                'message' => 'The media cannot be moved.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRenamed' => [
                'code' => 'RUM',
                'message' => 'The media cannot be renamed.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRestored' => [
                'code' => 'SAIL',
                'message' => 'The media cannot be restored.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeSaved' => [
                'code' => 'TREASURE',
                'message' => 'The media cannot be saved.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemoved' => [
                'code' => 'URCHIN',
                'message' => 'The media cannot be removed.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToCollection' => [
                'code' => 'HOARD',
                'message' => 'The media cannot be added to the collection.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromCollection' => [
                'code' => 'JOLLYROGER',
                'message' => 'The media cannot be removed from the collection.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToDisk' => [
                'code' => 'BUCCANEER',
                'message' => 'The media cannot be added to the disk.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromDisk' => [
                'code' => 'CANNON',
                'message' => 'The media cannot be removed from the disk.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToModel' => [
                'code' => 'CUTLASS',
                'message' => 'The media cannot be added to the model.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromModel' => [
                'code' => 'TENTACLE',
                'message' => 'The media cannot be removed from the model.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToUrl' => [
                'code' => 'SIREN',
                'message' => 'The media cannot be added to the URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromUrl' => [
                'code' => 'LURE',
                'message' => 'The media cannot be removed from the URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToCollectionUrl' => [
                'code' => 'HOOK',
                'message' => 'The media cannot be added to the collection URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromCollectionUrl' => [
                'code' => 'NET',
                'message' => 'The media cannot be removed from the collection URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToDiskUrl' => [
                'code' => 'SINK',
                'message' => 'The media cannot be added to the disk URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromDiskUrl' => [
                'code' => 'OAR',
                'message' => 'The media cannot be removed from the disk URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToModelUrl' => [
                'code' => 'ANCHOR',
                'message' => 'The media cannot be added to the model URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromModelUrl' => [
                'code' => 'BARREL',
                'message' => 'The media cannot be removed from the model URL.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToCollectionModel' => [
                'code' => 'CARGO',
                'message' => 'The media cannot be added to the collection model.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromCollectionModel' => [
                'code' => 'COMPASS',
                'message' => 'The media cannot be removed from the collection model.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeAddedToDiskModel' => [
                'code' => 'FLAG',
                'message' => 'The media cannot be added to the disk model.',
                'status' => 409
            ],
            'Filament\Spatie\MediaLibrary\Exceptions\MediaCannotBeRemovedFromDiskModel' => [
                'code' => 'GALLEON',
                'message' => 'The media cannot be removed from the disk model.',
                'status' => 409
            ],

            // Add more custom mappings as needed
        ];

        $exceptionClass = get_class($exception);

        return $errorMap[$exceptionClass] ?? [
            'code' => 'MYSTIC',
            'message' => 'An unexpected error has occurred, contact the Administrator.',
            'status' => 500
        ];
    }

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $doNotReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $doNotFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }
}
