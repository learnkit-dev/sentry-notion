# Sentry <--> Notion Bridge
## Installation Notion

- Create a new internal integration and copy the secret
- Share your databases you would like to access and create issues for with the integration

## Installation bridge

Add a Notion API secret to the .env file:

```
NOTION_API_SECRET="secret_..."
```

## Installation Sentry

- Create a “Custom Integration”
- Add the webhook URL
- Add the following schema to your integration

```json
{
  "elements": [
    {
      "type": "issue-link",
      "link": {
        "uri": "/sentry/issues/link",
        "required_fields": [
          {
            "type": "select",
            "label": "Issue",
            "name": "issue_id",
            "uri": "/sentry/issues",
            "async": true
          }
        ]
      },
      "create": {
        "uri": "/sentry/issues/create",
        "required_fields": [
          {
            "type": "select",
            "label": "Database",
            "name": "database",
            "uri": "/sentry/databases"
          },
          {
            "type": "text",
            "label": "Title",
            "name": "title"
          }
        ]
      }
    }
  ]
}
```
