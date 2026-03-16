<?php

function getStatusBadge(string $status): string
{
    $status = mb_strtolower($status);

    return match (true) {
        str_contains($status, 'done'),
        str_contains($status, 'concl') => 'success',

        str_contains($status, 'progress'),
        str_contains($status, 'exec'),
        str_contains($status, 'andamento') => 'warning',

        str_contains($status, 'block'),
        str_contains($status, 'bloq') => 'danger',

        str_contains($status, 'valid'),
        str_contains($status, 'review') => 'info',

        default => 'secondary',
    };
}

function estimateProgressByStatus(string $status): int
{
    $status = mb_strtolower($status);

    return match (true) {
        str_contains($status, 'backlog') => 5,
        str_contains($status, 'to do') => 10,
        str_contains($status, 'anal') => 20,
        str_contains($status, 'plan') => 35,
        str_contains($status, 'exec'),
        str_contains($status, 'progress'),
        str_contains($status, 'andamento') => 65,
        str_contains($status, 'valid'),
        str_contains($status, 'review') => 85,
        str_contains($status, 'done'),
        str_contains($status, 'concl') => 100,
        str_contains($status, 'bloq') => 40,
        default => 15,
    };
}

function getEpicNameFromParent(?array $parent): string
{
    if (!$parent) {
        return 'Sem épico';
    }

    $fields = $parent['fields'] ?? [];
    return $fields['summary'] ?? ($parent['key'] ?? 'Sem épico');
}