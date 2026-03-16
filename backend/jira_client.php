<?php

require_once __DIR__ . '/config.php';

class JiraClient
{
    private string $baseUrl;
    private string $email;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(JIRA_BASE_URL, '/');
        $this->email = JIRA_EMAIL;
        $this->token = JIRA_API_TOKEN;
    }

    public function request(string $method, string $endpoint, array $query = []): array
    {
        $url = $this->baseUrl . $endpoint;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_USERPWD => $this->email . ':' . $this->token,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new Exception("Erro Jira HTTP {$httpCode}: {$response}");
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function searchIssues(string $jql, int $maxResults = 100, array $fields = []): array
    {
        return $this->request(
            'GET',
            '/rest/api/3/search/jql',
            [
                'jql' => $jql,
                'maxResults' => $maxResults,
                'fields' => implode(',', $fields),
            ]
        );
    }
}