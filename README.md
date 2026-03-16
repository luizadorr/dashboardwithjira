# Jira Metrics Dashboard: PHP + API REST

Este projeto é uma ferramenta de Business Intelligence (BI) simplificada que consome dados em tempo real de um board no Jira Software via API REST para transformá-los em indicadores visuais (KPIs) em um dashboard online.

O objetivo principal é oferecer uma visão analítica de gargalos, produtividade e saúde do backlog que muitas vezes são difíceis de visualizar apenas com a interface padrão do Jira.

# Funcionalidades
- Integração Direta: Consumo de dados via endpoints oficiais da Atlassian (Jira REST API).
- Visualização de Status: Gráficos que mostram a distribuição de tarefas (To Do, In Progress, Done).
- Monitoramento de Backlog: Identificação rápida de volume de demandas por tipo (Task, Bug, Story).
- Interface Responsiva: Dashboard construído com foco na experiência do usuário e clareza de dados.

# Tecnologias Utilizadas
- Backend: PHP (Consumo de API e tratamento de dados JSON).
- Frontend: HTML5, CSS3 e Bootstrap (Interface baseada em template profissional).
- Integração: API REST do Jira (Autenticação via API Token).

# Como funciona a Integração
O backend em PHP realiza requisições autenticadas para o Jira Cloud. O fluxo consiste em:

- Requisição: O PHP solicita os dados de um board específico.
- Processamento: Os dados retornados em JSON são filtrados e contados de acordo com a lógica de negócio.
- Renderização: O PHP injeta esses valores dinamicamente nos componentes do template Bootstrap.

# Configuração Local
- Clone o repositório
- Configure as credenciais: Já criei um config_example para inserção dos api token, email, etc.

# Por que este projeto?
Como desenvolvedora, acredito que a tecnologia deve servir para otimizar a gestão. Este projeto demonstra habilidades em:

- Consumo de APIs de terceiros: Lidar com documentações externas e autenticação segura.
- Manipulação de dados: Transformar dados brutos em informações úteis.
- Agilidade: Uso de frameworks (Bootstrap) para entrega rápida de interfaces profissionais.