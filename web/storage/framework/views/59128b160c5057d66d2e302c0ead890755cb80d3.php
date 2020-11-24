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
		<?php for($i = 0; $i < count($data); $i++): ?>
			<tr>
				<td><?php echo e($data[$i]['name']); ?></td>
				<td><?php echo e($data[$i]['quantity']); ?></td>
				<td><?php echo e($data[$i]['amount']); ?></td>
				<td><?php echo e($data[$i]['amount'] * $data[$i]['quantity']); ?></td>
			</tr>
		<?php endfor; ?>
	</tbody>
</table>
<?php /**PATH D:\Web Development\chibog\web\resources\views/emails/order.blade.php ENDPATH**/ ?>