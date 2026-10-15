<?php

namespace App\Core;

/**
 * Lightweight input validator.
 *
 * Usage:
 *   $v = new Validator($input);
 *   $v->required('first_name', 'First name')
 *     ->maxLength('first_name', 50)
 *     ->email('email', 'Email address');
 *   if ($v->fails()) { ... $v->errors() ... }
 */
class Validator
{
    /** @var array Input under validation */
    private $input;

    /** @var array Field => list of error messages */
    private $errors = [];

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * A field must be present and non-empty.
     */
    public function required(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? null;

        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->addError($field, "{$label} is required.");
        }
        return $this;
    }

    /**
     * Maximum string length.
     */
    public function maxLength(string $field, int $max, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '' && mb_strlen($value) > $max) {
            $this->addError($field, "{$label} must not exceed {$max} characters.");
        }
        return $this;
    }

    /**
     * Minimum string length.
     */
    public function minLength(string $field, int $min, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '' && mb_strlen($value) < $min) {
            $this->addError($field, "{$label} must be at least {$min} characters.");
        }
        return $this;
    }

    /**
     * Value must match a regular expression.
     */
    public function pattern(string $field, string $regex, string $message): self
    {
        $value = $this->input[$field] ?? '';
        if (is_string($value) && $value !== '' && !preg_match($regex, $value)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Valid e-mail address.
     */
    public function email(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$label} must be a valid e-mail address.");
        }
        return $this;
    }

    /**
     * Value must be one of the allowed options.
     */
    public function in(string $field, array $allowed, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '' && !in_array($value, $allowed, true)) {
            $this->addError($field, "Please choose a valid option for {$label}.");
        }
        return $this;
    }

    /**
     * Value must be a positive integer (or numeric id).
     */
    public function integer(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '' && !ctype_digit((string) $value)) {
            $this->addError($field, "{$label} must be a number.");
        }
        return $this;
    }

    /**
     * Value must look like a calendar date (YYYY-MM-DD).
     */
    public function date(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->input[$field] ?? '';

        if (is_string($value) && $value !== '') {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if ($d === false || $d->format('Y-m-d') !== $value) {
                $this->addError($field, "{$label} must be a valid date (YYYY-MM-DD).");
            }
        }
        return $this;
    }

    /**
     * Compare two fields for equality (e.g. password confirmation).
     */
    public function matches(string $field, string $otherField, string $message): self
    {
        if (($this->input[$field] ?? null) !== ($this->input[$otherField] ?? null)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Custom closure rule: fn($value): bool
     */
    public function custom(string $field, callable $rule, string $message): self
    {
        $value = $this->input[$field] ?? null;
        if (!$rule($value)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * True when at least one rule failed.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * All error messages, keyed by field.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Flat list of error messages (handy for flash messages).
     */
    public function messages(): array
    {
        $all = [];
        foreach ($this->errors as $messages) {
            foreach ($messages as $message) {
                $all[] = $message;
            }
        }
        return $all;
    }
}
