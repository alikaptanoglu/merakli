<?php
/**
 * View template for editing users in admin context.
 *
 * Same form with add layout is used here. However old data is injected here
 * using $user variable.
 *
 * @author Midori Kocak 2014
 *
 */
?>

<form action="<?= LINK_PREFIX ?>/Users/Edit/<?= $user['id'] ?>"
	method="post">
	<div class="row">
		<div class="large-12 columns">
			<label>Kullanıcı Adı <input id="username" name="username" type="text"
				placeholder="Kullanıcı Adı" value="<?= $user['username'] ?>" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<label>E-posta Adresi <input id="email" name="email" type="text"
				placeholder="E-Posta Adresi" value="<?= $user['email'] ?>" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<label>Parola <input id="password" name="password" type="password"
				placeholder="Parola" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<label>Parola Tekrarı <input id="password2" name="password2"
				type="password" placeholder="Parola Tekrarı" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<button type="submit">Submit</button>
		</div>
	</div>
</form>