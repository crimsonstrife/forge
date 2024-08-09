<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings as SpatieSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Settings extends SpatieSettings
{
    use HasFactory;

    protected $fillable = [
        'group',
        'name',
        'locked',
        'payload',
        'is_encrypted',
        'encryption_key',
        'encryption_type',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Geth the value of the Encryption Type attribute. If the value is null, it will return 'crypt'.
     * Possible values are 'crypt', 'bcrypt', 'argon', and 'argon2id' but you can add more encryption types.
     * @param string $value
     * @return string
     */
    protected function getEncryptionTypeAttribute($value): string
    {
        return $value ?? 'crypt';
    }

    /**
     * Set the value of the Encryption Type attribute.
     * @param string $value
     * @return void
     */
    protected function setEncryptionTypeAttribute(string $value): void
    {
        $this->attributes['encryption_type'] = $value;
    }

    /**
     * Get the encryption hash mode.
     * @param string $value
     * @return mixed
     */
    protected function setEncryptionMode(string $encryptionType): mixed
    {
        switch ($encryptionType) {
            case 'crypt':
                return NULL;
            case 'bcrypt':
                return PASSWORD_BCRYPT;
            case 'argon':
                return PASSWORD_ARGON2I;
            case 'argon2id':
                return PASSWORD_ARGON2ID;
            default:
                return PASSWORD_DEFAULT;
        }
    }

    /**
     * Encrypt the value.
     * @param string $value
     * @return string
     */
    protected function encryptValue(string $value): string
    {
        $mode = $this->setEncryptionMode($this->getEncryptionTypeAttribute($this->encryption_type));

        switch ($mode) {
            case PASSWORD_BCRYPT:
                return password_hash($value, PASSWORD_BCRYPT);
            case PASSWORD_ARGON2I:
                return password_hash($value, PASSWORD_ARGON2I);
            case PASSWORD_ARGON2ID:
                return password_hash($value, PASSWORD_ARGON2ID);
            case PASSWORD_DEFAULT:
                return password_hash($value, PASSWORD_DEFAULT);
            case NULL:
                return Crypt::encryptString($value);
            default:
                return Crypt::encryptString($value);
        }
    }

    /**
     * Decrypt the value.
     * @param string $value
     * @return string | bool
     */
    protected function decryptValue(string $value): string | bool
    {
        $mode = $this->setEncryptionMode($this->getEncryptionTypeAttribute($this->encryption_type));

        switch ($mode) {
            case PASSWORD_BCRYPT:
                return password_verify($value, PASSWORD_BCRYPT);
            case PASSWORD_ARGON2I:
                return password_verify($value, PASSWORD_ARGON2I);
            case PASSWORD_ARGON2ID:
                return password_verify($value, PASSWORD_ARGON2ID);
            case PASSWORD_DEFAULT:
                return password_verify($value, PASSWORD_DEFAULT);
            case NULL:
                return Crypt::decryptString($value);
            default:
                return Crypt::decryptString($value);
        }
    }

    /**
     * Set the payload attribute.
     * @param array $value
     * @return void
     * @throws \Exception
     */
    protected function setPayloadAttribute(array $value): void
    {
        $payload = [];

        //check if the payload is encrypted
        if ($this->is_encrypted) {
            foreach ($value as $key => $val) {
                //encrypt the value
                $payload[$key] = $this->encryptValue($val);
            }
        } else {
            $payload = $value;
        }

        //encode the payload
        $this->attributes['payload'] = json_encode($payload);
    }

    /**
     * Get the payload attribute.
     * @param string $value
     * @return array
     */
    protected function getPayloadAttribute(string $value): array
    {
        $payload = json_decode($value, true);

        //check if the payload is encrypted
        if ($this->is_encrypted) {
            foreach ($payload as $key => $val) {
                //get the encryption mode
                $mode = $this->setEncryptionMode($this->getEncryptionTypeAttribute($this->encryption_type));

                //depending on the encryption mode, we will return the decrypted value or specify the original value is hashed
                switch ($mode) {
                    case PASSWORD_BCRYPT:
                        //if the value is hashed, we will return a nested array with the boolean result and a 'Hashed Value' string
                        $payload[$key] = array(
                            'is_hashed' => password_verify($val, PASSWORD_BCRYPT),
                            'value' => 'Hashed Value'
                        );
                        break;
                    case PASSWORD_ARGON2I:
                        $payload[$key] = array(
                            'is_hashed' => password_verify($val, PASSWORD_ARGON2I),
                            'value' => 'Hashed Value'
                        );
                        break;
                    case PASSWORD_ARGON2ID:
                        $payload[$key] = array(
                            'is_hashed' => password_verify($val, PASSWORD_ARGON2ID),
                            'value' => 'Hashed Value'
                        );
                        break;
                    case PASSWORD_DEFAULT:
                        $payload[$key] = array(
                            'is_hashed' => password_verify($val, PASSWORD_DEFAULT),
                            'value' => 'Hashed Value'
                        );
                        break;
                    case NULL:
                        $payload[$key] = array(
                            'is_hashed' => false,
                            'value' => Crypt::decryptString($val)
                        );
                        break;
                    default:
                        $payload[$key] = array(
                            'is_hashed' => false,
                            'value' => Crypt::decryptString($val)
                        );
                        break;
                }
            }
        }

        return $payload;
    }

    /**
     * Get the group name.
     * @return string
     */
    public static function group(): string
    {
        return 'general';
    }
}
