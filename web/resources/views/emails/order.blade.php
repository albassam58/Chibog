<h2>Orders</h2>
<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Quantity</th>
			<th>Amount</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		@for($i = 0; $i < count($data); $i++)
			<tr>
				<td>{{ $data[$i]['name'] }}</td>
				<td>{{ $data[$i]['quantity'] }}</td>
				<td>{{ $data[$i]['amount'] }}</td>
				<td>{{ $data[$i]['amount'] * $data[$i]['quantity'] }}</td>
			</tr>
		@endfor
	</tbody>
</table>
