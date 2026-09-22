# Bulk Global Carrier Lookup — `global_carrier_batch` | CarrierLookup

Bulk carrier lookup is built for lists from a thousand numbers upward: upload a file, the task runs in the background, and you download a result archive when it completes. Pick the country the numbers belong to when you upload: it is submitted with the file, completes any number that has no country code, and the file is checked against it.

This is the official CarrierLookup example repository for **one** bulk product, `global_carrier_batch`. It is an asynchronous task: upload a file, get a task id immediately, poll the task, and download the result file when it finishes.

- **Product page:** https://carrierlookup.online/products/global_carrier_batch
- **API documentation:** https://carrierlookup.online/api-docs
- **API base URL:** `https://carrierlookup.online`
- **Authentication:** `X-API-Key`
- **Get an API key:** https://carrierlookup.online/register

## What is it usually used for?

- List segmentation
- Routing cost review
- Database hygiene

## What does the result file contain?

| Column | Example | Meaning |
|---|---|---|
| `number` | `17253100591` | The original submitted E.164 number, used to match carrier and geographic results back to the original list record. |
| `carrier` | `T-Mobile` | The customer-facing carrier name when identifiable. Use it for service routing or CRM enrichment; do not infer that no carrier exists when the value is empty. |
| `underlying_carrier` | `` | The underlying network-carrier information when identifiable. It can differ from carrier for resellers, virtual operators or ported numbers. |
| `number_type` | `Fixed Line or Mobile` | The returned line type, such as mobile, fixed line or another identifiable category. It can support routing and segmentation when used with your own business rules. |
| `country_code` | `US` | The returned country or territory code for baseline grouping and regional reporting across international lists. It is not a person’s current location. |
| `region` | `NV` | The returned region or state/province context when available. It supports regional analysis and should not be treated as an exact geographic location. |
| `city` | `LAS VEGAS` | The returned city-level context when available. Data sources and number allocation can affect accuracy, so validate it against your own address or consent records when needed. |

The result is a **point-in-time signal**, not a verdict, and not identity data. It describes what the provider reported at the moment the task ran.

## How do I submit a task?

Upload a `.txt` or `.csv` with **one phone number per line**, 1,000–100,000 valid entries. A number list must come from a **single country**, given as `country` — the upstream uses it to expand bare national numbers.

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

## Bulk task or realtime check?

`POST /api/v1/bulk-tasks` (this repository) takes a file of 1,000–100,000 entries and answers later — that is the shape for list cleaning, campaign preparation and enrichment runs. A **realtime** check (`POST /api/v1/check`, or `POST /api/v1/batch-check` for up to 100 identifiers) answers inside the same HTTP response, for a signup form or a live lookup. The two are separate endpoints and are not interchangeable; see the other repositories under [carrierlookup](https://github.com/carrierlookup).

## What about billing?

The whole file is reserved on submit. When the task finishes you are charged only for the entries that were actually checked and the remainder is returned. A failed task is refunded in full.

## What are the limits and error codes?

| Limit | Value |
|---|---|
| Entries per task | 1,000–100,000 |
| File formats | `.txt`, `.csv`, one entry per line |
| Products per task | 1 (`global_carrier_batch`) |
| Country | required, exactly one per task |
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

## Official resources and responsible use

- **This product:** https://carrierlookup.online/products/global_carrier_batch
- **All products:** https://carrierlookup.online/products
- **Documentation:** https://carrierlookup.online/api-docs
- **Registration:** https://carrierlookup.online/register
- **Pricing:** https://carrierlookup.online/pricing
- **OpenAPI contract:** [`openapi.yaml`](openapi.yaml)
- **License:** [MIT](LICENSE) for the sample code

Submit only data you are authorized to process, and comply with applicable privacy laws and platform terms. Report the signal you received as a signal; do not relabel it downstream as a conclusion it does not support.

---

*Last reviewed: 2026-09-22 · Maintained by CarrierLookup. Canonical documentation: https://carrierlookup.online/api-docs*
