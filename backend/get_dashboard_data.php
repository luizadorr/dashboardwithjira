<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/jira_client.php';
require_once __DIR__ . '/helpers.php';

try {
    $jira = new JiraClient();

    $fields = [
        'summary',
        'status',
        'assignee',
        'priority',
        'duedate',
        'labels',
        'components',
        'issuetype',
        'parent',
        'created'
    ];

    $jql = 'project = ' . JIRA_PROJECT_KEY . ' ORDER BY created DESC';

    $data = $jira->searchIssues($jql, 100, $fields);
    $issues = $data['issues'] ?? [];

    $formattedIssues = [];
    $statusCount = [];
    $priorityCount = [];
    $epicCount = [];

    $kpis = [
        'total' => 0,
        'done' => 0,
        'blocked' => 0,
        'in_progress' => 0,
        'overdue' => 0,
        'epics' => 0,
        'tasks' => 0
    ];

    $today = new DateTime('today');
    $scheduleCount = [];

    foreach ($issues as $issue) {
        $f = $issue['fields'] ?? [];

        $key = $issue['key'] ?? '';
        $summary = $f['summary'] ?? 'Sem resumo';
        $status = $f['status']['name'] ?? 'Sem status';
        $priority = $f['priority']['name'] ?? 'Sem prioridade';
        $assignee = $f['assignee']['displayName'] ?? 'Não atribuído';
        $dueDate = $f['duedate'] ?? null;
        $labels = $f['labels'] ?? [];
        $issueType = $f['issuetype']['name'] ?? 'Task';
        $parent = $f['parent'] ?? null;

        $components = [];
        if (!empty($f['components'])) {
            foreach ($f['components'] as $component) {
                $components[] = $component['name'] ?? '';
            }
        }

        $epicName = getEpicNameFromParent($parent);
        $progress = estimateProgressByStatus($status);
        $badge = getStatusBadge($status);

        $statusCount[$status] = ($statusCount[$status] ?? 0) + 1;
        $priorityCount[$priority] = ($priorityCount[$priority] ?? 0) + 1;
        $epicCount[$epicName] = ($epicCount[$epicName] ?? 0) + 1;

        $kpis['total']++;

        $statusLower = mb_strtolower($status);
        $issueTypeLower = mb_strtolower($issueType);

        if (str_contains($statusLower, 'done') || str_contains($statusLower, 'concl')) {
            $kpis['done']++;
        }

        if (str_contains($statusLower, 'bloq') || str_contains($statusLower, 'block')) {
            $kpis['blocked']++;
        }

        if (
            str_contains($statusLower, 'exec') ||
            str_contains($statusLower, 'progress') ||
            str_contains($statusLower, 'andamento')
        ) {
            $kpis['in_progress']++;
        }

        if ($dueDate) {
            $scheduleCount[$dueDate] = ($scheduleCount[$dueDate] ?? 0) + 1;
            if ($dueDate < $today && !str_contains($statusLower, 'done') && !str_contains($statusLower, 'concl')) {
                $kpis['overdue']++;
            }
        }

        if (str_contains($issueTypeLower, 'epic')) {
            $kpis['epics']++;
        } else {
            $kpis['tasks']++;
        }

        $formattedIssues[] = [
            'key' => $key,
            'summary' => $summary,
            'status' => $status,
            'status_badge' => $badge,
            'assignee' => $assignee,
            'priority' => $priority,
            'due_date' => $dueDate,
            'progress' => $progress,
            'issue_type' => $issueType,
            'epic' => $epicName,
            'components' => $components,
            'labels' => $labels
        ];
    }

    echo json_encode([
        'success' => true,
        'kpis' => $kpis,
        'status_chart' => [
            'labels' => array_keys($statusCount),
            'values' => array_values($statusCount)
        ],
        'priority_chart' => [
            'labels' => array_keys($priorityCount),
            'values' => array_values($priorityCount)
        ],
        'epic_chart' => [
            'labels' => array_keys($epicCount),
            'values' => array_values($epicCount)
        ],
        'schedule_chart' => [
        'labels' => array_keys($scheduleCount),
        'values' => array_values($scheduleCount)
        ],
        'issues' => $formattedIssues
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}