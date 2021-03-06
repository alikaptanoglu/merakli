<?php
/**
 * Lists Users in admin context.
 *
 * Users are listed and operational links are generated by their id.
 *
 * @author Midori Kocak 2014
 *
 */
?>
<h2>Users</h2>
<table>
	<thead>
		<tr>
			<th>Id</th>
			<th>Username</th>
			<th>E-mail</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
    <?php
    foreach ($users as $user) :
        ?>
        <tr>
			<td><?= $user['id'] ?></td>
			<td><?= htmlspecialchars($user['username']) ?></td>
			<td><?= htmlspecialchars($user['email']) ?></td>
			<td><a href="<?= LINK_PREFIX ?>/Users/Edit/<?= $user['id'] ?>">Update</a>
				<a href="<?= LINK_PREFIX ?>/Users/Delete/<?= $user['id'] ?>">Delete</a></td>
		</tr>
    <?php
    endforeach
    ;
    ?>
    </tbody>
</table>