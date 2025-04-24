# Laravel Dynamic Query Filtering

This service provides a flexible and reusable way to dynamically filter Eloquent queries in Laravel based on structured input. It supports filtering on direct model fields, nested relationships, date conditions, and more.

---

## ✨ Features

- Dynamic filters using field/operator/value structure
- Supports:
  - `where`
  - `whereIn`
  - `whereDate`
  - `whereRelation`
  - `whereHas` (with nested filters)
- Deep relationship filtering (e.g., `author.owner.name`)
- Extendable and clean structure

---


## 🤔 How to Use

### Controller Example

```php
use App\Models\Post;
use QueryFilter;
use Illuminate\Http\Request;

public function index(Request $request)
{
    $filters = $request->input('filters', []);

    $query = Post::query()->with(['author', 'author.owner']);

    $posts = (new QueryFilter())->apply($query, $filters)->paginate();

    return response()->json($posts);
}
```

---

## 📂 Example Request Payload or you can set this from controller

```json
{
  "filters": [
    { "field": "title", "operator": "like", "value": "laravel" },
    { "field": "category_id", "operator": "=", "value": 2 },
    { "field": "author.name", "operator": "like", "value": "john" },
    { "field": "published_at", "operator": "date", "value": "2025-04-24" },
    {
      "field": "author",
      "operator": "has",
      "value": [
        { "field": "author.owner.name", "operator": "like", "value": "admin" }
      ]
    }
  ]
}
```

---

## 📅 Supported Operators

| Operator   | Description                          |
|------------|--------------------------------------|
| `=`        | Equal to                             |
| `like`     | Partial match (e.g., `%value%`)      |
| `in`       | Array match using `whereIn`          |
| `date`     | Date comparison via `whereDate`      |
| `has`      | Nested relationship filter via `whereHas` |
| `<`, `>`, `<=`, `>=` | Standard comparison operators |

---

## 🔧 Extending Support

You can enhance the logic further to include:
- `!=` (not equal)
- `not in`
- Null checks
- Between queries

Just modify the `applyCondition()` method to suit your use case.

---


