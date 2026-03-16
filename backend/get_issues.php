<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/jira_client.php';
require_once __DIR__ . '/helpers.php';

try {
    $jira = new JiraClient();

    $data = $jira->searchIssues(
        'project = ' . JIRA_PROJECT_KEY . ' ORDER BY created DESC',
        50,
        ['summary', 'status', 'assignee', 'priority', 'duedate', 'components', 'issuetype']
    );

    $issues = [];

    foreach (($data['issues'] ?? []) as $issue) {
        $fields = $issue['fields'] ?? [];

        $issues[] = [
            'key' => $issue['key'] ?? '',
            'summary' => $fields['summary'] ?? '',
            'status' => $fields['status']['name'] ?? '',
            'assignee' => $fields['assignee']['displayName'] ?? 'Não atribuído',
            'priority' => $fields['priority']['name'] ?? 'Sem prioridade',
            'due_date' => $fields['duedate'] ?? null,
            'components' => array_map(fn($c) => $c['name'] ?? '', $fields['components'] ?? []),
            'issue_type' => $fields['issuetype']['name'] ?? 'Task',
            'progress' => estimateProgressByStatus($fields['status']['name'] ?? '')
        ];
    }

    echo json_encode([
        'success' => true,
        'issues' => $issues
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}