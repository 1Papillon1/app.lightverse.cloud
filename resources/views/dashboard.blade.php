<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard</title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; background: #f4f6fa; color: #1f2937; font-family: Arial, sans-serif; }
		.dashboard { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }
		.header { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 28px; }
		h1 { margin: 0 0 6px; font-size: 28px; }
		.subtitle { margin: 0; color: #6b7280; }
		.button { border: 0; border-radius: 8px; padding: 11px 16px; background: #4f46e5; color: #fff; font-weight: 600; cursor: pointer; }
		.stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
		.card, .table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(15, 23, 42, .04); }
		.card { padding: 20px; }
		.card-label { color: #6b7280; font-size: 14px; }
		.card-value { margin-top: 10px; font-size: 26px; font-weight: 700; }
		.card-change { margin-top: 6px; color: #15803d; font-size: 13px; }
		.table-card { overflow: hidden; }
		.table-heading { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 20px 22px; }
		h2 { margin: 0; font-size: 18px; }
		.view-all { color: #4f46e5; font-size: 14px; text-decoration: none; }
		.table-wrap { overflow-x: auto; }
		table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
		th, td { padding: 14px 22px; border-top: 1px solid #e5e7eb; }
		th { background: #f9fafb; color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
		td { font-size: 14px; }
		.status { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
		.paid { background: #dcfce7; color: #166534; }
		.pending { background: #fef3c7; color: #92400e; }
		.overdue { background: #fee2e2; color: #991b1b; }
		@media (max-width: 800px) { .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
		@media (max-width: 520px) { .dashboard { padding: 24px 16px; } .header { align-items: flex-start; flex-direction: column; } .stats { gap: 10px; } .card { padding: 16px; } .card-value { font-size: 22px; } }
	</style>
</head>
<body>
	<main class="dashboard">
		<header class="header">
			<div>
				<h1>Dashboard</h1>
				<p class="subtitle">Here’s an overview of your business.</p>
			</div>
			<button class="button" type="button">+ New invoice</button>
		</header>

		<section class="stats" aria-label="Dashboard summary">
			<article class="card">
				<div class="card-label">Total revenue</div>
				<div class="card-value">$24,780</div>
				<div class="card-change">↑ 12.8% this month</div>
			</article>
			<article class="card">
				<div class="card-label">Orders</div>
				<div class="card-value">1,248</div>
				<div class="card-change">↑ 8.2% this month</div>
			</article>
			<article class="card">
				<div class="card-label">Customers</div>
				<div class="card-value">684</div>
				<div class="card-change">↑ 5.4% this month</div>
			</article>
			<article class="card">
				<div class="card-label">Pending invoices</div>
				<div class="card-value">18</div>
				<div class="card-change">Requires attention</div>
			</article>
		</section>

		<section class="table-card" aria-labelledby="recent-orders-heading">
			<div class="table-heading">
				<h2 id="recent-orders-heading">Recent orders</h2>
				<a class="view-all" href="#orders">View all</a>
			</div>
			<div class="table-wrap">
				<table id="orders">
					<thead>
						<tr>
							<th>Order</th>
							<th>Customer</th>
							<th>Date</th>
							<th>Amount</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>#ORD-1048</td>
							<td>Olivia Martin</td>
							<td>Jun 18, 2025</td>
							<td>$320.00</td>
							<td><span class="status paid">Paid</span></td>
						</tr>
						<tr>
							<td>#ORD-1047</td>
							<td>Jackson Lee</td>
							<td>Jun 17, 2025</td>
							<td>$145.00</td>
							<td><span class="status pending">Pending</span></td>
						</tr>
						<tr>
							<td>#ORD-1046</td>
							<td>Isabella Nguyen</td>
							<td>Jun 16, 2025</td>
							<td>$590.00</td>
							<td><span class="status paid">Paid</span></td>
						</tr>
						<tr>
							<td>#ORD-1045</td>
							<td>William Kim</td>
							<td>Jun 14, 2025</td>
							<td>$89.00</td>
							<td><span class="status overdue">Overdue</span></td>
						</tr>
					</tbody>
				</table>
			</div>
		</section>
	</main>
</body>
</html>
