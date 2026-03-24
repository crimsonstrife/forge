<?php

namespace App\Services\Issues;

final class IssueQueryParser
{
    /**
     * @return array{
     *     terms: array<int, string>,
     *     clauses: array<int, array{field:string, operator:string, value:string}>
     * }
     */
    public function parse(string $query): array
    {
        $clauses = [];
        $terms = [];

        foreach ($this->tokenize($query) as $token) {
            if (preg_match('/^(?<field>[a-z_]+)(?<operator>:|<=|>=|!=|=|<|>)(?<value>.+)$/i', $token, $matches) !== 1) {
                $terms[] = trim($token);

                continue;
            }

            $value = trim((string) ($matches['value'] ?? ''));

            if ($value === '') {
                $terms[] = trim($token);

                continue;
            }

            $clauses[] = [
                'field' => strtolower((string) $matches['field']),
                'operator' => (string) $matches['operator'],
                'value' => $value,
            ];
        }

        return [
            'terms' => array_values(array_filter($terms, static fn (string $term): bool => $term !== '')),
            'clauses' => $clauses,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function tokenize(string $query): array
    {
        $tokens = [];
        $current = '';
        $quote = null;
        $length = mb_strlen($query);

        for ($index = 0; $index < $length; $index++) {
            $char = mb_substr($query, $index, 1);

            if ($quote === null) {
                if (ctype_space($char)) {
                    if ($current !== '') {
                        $tokens[] = $current;
                        $current = '';
                    }

                    continue;
                }

                if ($char === '"' || $char === "'") {
                    $quote = $char;

                    continue;
                }

                $current .= $char;

                continue;
            }

            if ($char === '\\' && $index + 1 < $length) {
                $index++;
                $current .= mb_substr($query, $index, 1);

                continue;
            }

            if ($char === $quote) {
                $quote = null;

                continue;
            }

            $current .= $char;
        }

        if ($current !== '') {
            $tokens[] = $current;
        }

        return $tokens;
    }
}
