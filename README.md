# Bulk Carrier Lookup API — Asynchronous List Processing | CarrierLookup

The **CarrierLookup Bulk Carrier Lookup API** processes whole lists asynchronously: upload a file, get a task id immediately, poll the task, and download the result file when it finishes. This is the official CarrierLookup example repository for the bulk endpoints, with runnable integrations in seven languages.

- **Product overview:** https://carrierlookup.online/products
- **API documentation:** https://carrierlookup.online/api-docs
- **API base URL:** `https://carrierlookup.online`
- **Authentication:** `X-API-Key`
- **Get an API key:** https://carrierlookup.online/register

## Bulk tasks or realtime checks — which endpoint do I want?

CarrierLookup exposes two different shapes, and they are not interchangeable:

| | Realtime check | Bulk task (this repository) |
|---|---|---|
| Endpoint | `POST /api/v1/check`, `POST /api/v1/batch-check` | `POST /api/v1/bulk-tasks` |
| Input | one identifier, or up to 100 per request | a `.txt`/`.csv` file, 1,000–100,000 lines |
| Response | the result, in the same response | a task id; the result arrives later |
| Use it for | a signup form, a checkout step, a live lookup | list cleaning, campaign preparation, enrichment runs |

Realtime examples live in the other repositories under the [CarrierLookup organization](https://github.com/carrierlookup). Everything below is the asynchronous path.

## Which products can a task use?

One task carries **one product** and **one country**. A list spanning several markets becomes one task per market.

| Product code | Name | Result columns |
|---|---|---|
| `global_carrier_batch` | Global Carrier Lookup (File) | `number`, `carrier`, `underlying_carrier`, `number_type`, `country_code`, `region`, `city` |

Each product returns a **signal**, not a verdict. Carrier data is affected by number portability and is not a subscriber-identity lookup.

## How do I submit a task?

Upload a `.txt` or `.csv` with **one phone number per line**, 1,000–100,000 valid entries, from a single country.

```bash
curl -X POST 'https://carrierlookup.online/api/v1/bulk-tasks' \
  -H 'X-API-Key: YOUR_API_KEY' \
  -F 'product=global_carrier_batch' \
  -F 'country=US' \
  -F 'file=@numbers.txt'
```

The response returns the task id and `status=processing`, along with the server-side quote taken before processing starts.

## How do I poll a task and download the result?

```bash
curl -fsS 'https://carrierlookup.online/api/v1/bulk-tasks/TASK_ID' -H 'X-API-Key: YOUR_API_KEY'
```

`status` is `processing`, `success` or `failed` — there is no progress percentage to poll for. **Do not poll more often than once every 30 seconds.** When the task succeeds the response carries the result-file download link.

## What about billing?

The whole file is reserved on submit. When the task finishes you are charged only for the entries that were actually checked and the remainder is returned. A failed task is refunded in full.

## What are the limits and error codes?

| Limit | Value |
|---|---|
| Entries per task | 1,000–100,000 |
| File formats | `.txt`, `.csv`, one entry per line |
| Countries per task | 1 |
| Products per task | 1 |
| Poll interval | no more than once per 30 seconds |

`40000`/`40001` are request or file errors; `40100` is an invalid key; `40200` is insufficient balance; `42900` is rate limited — honour `Retry-After`; `50300` is temporary maintenance. `GET /api/v1/balance` returns the current balance in `data.balance_micros`.

Keep the key on a trusted server and read it from `CARRIERLOOKUP_API_KEY`. Never commit it or expose it in production browser code.

## Runnable examples in seven languages

| Language | Example |
|---|---|
| Python | [`examples/python`](examples/python) |
| Node.js | [`examples/nodejs`](examples/nodejs) |
| Go | [`examples/go`](examples/go) |
| Java | [`examples/java`](examples/java) |
| C# | [`examples/csharp`](examples/csharp) |
| PHP | [`examples/php`](examples/php) |
| Shell / curl | [`examples/shell`](examples/shell) |

Each example submits a task and then polls it; extend the poll into a loop that respects the 30-second interval.

## Official resources and responsible use

- **Products:** https://carrierlookup.online/products
- **Documentation:** https://carrierlookup.online/api-docs
- **Registration:** https://carrierlookup.online/register
- **Pricing:** https://carrierlookup.online/pricing
- **OpenAPI contract:** [`openapi.yaml`](openapi.yaml)
- **License:** [MIT](LICENSE) for the sample code

Submit only data you are authorized to process, and comply with applicable privacy laws and platform terms. Report the signal you received as a signal; do not relabel it downstream as a conclusion it does not support.

---

*Last reviewed: 2026-09-22 · Maintained by CarrierLookup. Canonical documentation: https://carrierlookup.online/api-docs*
