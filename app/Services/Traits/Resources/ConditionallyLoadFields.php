<?php

declare(strict_types=1);

namespace App\Services\Traits\Resources;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;

/**
 * Trait ConditionallyLoadFields
 * @package App\Services\Traits\Resources
 */
trait ConditionallyLoadFields
{
    /**
     * Retrieve a value based on a given condition.
     *
     * @param Request $request
     * @param string $kebabCaseName Request key name in kebab case
     * @param Closure|mixed $value
     * @param string $requestField
     * @return MissingValue|mixed
     */
    public function whenFields(Request $request, string $kebabCaseName, $value, string $requestField = 'fields'): mixed
    {
        $shouldReturn = $request->missing($requestField)
            || $this->requestHasFields($request, $kebabCaseName, $requestField);

        return $this->when($shouldReturn, $value);
    }

    /**
     * Retrieve a value based on a given condition.
     *
     * @param Request $request
     * @param string $kebabCaseName Request key name in kebab case
     * @param Closure|mixed $value
     * @param string $requestField
     * @return MissingValue|mixed
     */
    public function whenFieldRequired(Request $request, string $kebabCaseName, $value, string $requestField = 'fields'): mixed
    {
        return $this->when($this->requestHasFields($request, $kebabCaseName, $requestField), $value);
    }

    /**
     * We need to convert to camel first and then to get kebab
     *
     * @param Request $request
     * @param string $fieldName
     * @param string $requestField
     * @return bool
     */
    public function requestHasFields(Request $request, string $fieldName, string $requestField): bool
    {
        return $request->has($requestField) && in_array(Str::kebab(Str::camel($fieldName)), $request->{$requestField});
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getRequestedFields(Request $request): array
    {
        $callback = fn(string $field, string $resourceField) => [
            $resourceField => $this->whenFields(
                $request,
                $field,
                $this[$field]
            )
        ];

        return collect($this->conditionalFields)->mapWithKeys($callback)->toArray();
    }
}
