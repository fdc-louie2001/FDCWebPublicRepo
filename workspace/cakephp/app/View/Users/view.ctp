<div class="users view">
	<h2><?php echo __('User'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Profile Picture'); ?></dt>
		<dd>
			<?php
			$profilePic = h($user['User']['profilePic']);

			echo $this->Html->image($profilePic, ['alt' => 'Profile Picture', 'class' => 'profile-pic',]); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($user['User']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Gender'); ?></dt>
		<dd>
			<?php echo h($user['User']['gender']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('BirthDate'); ?></dt>
		<dd>
			<?php $birthDate = $user['User']['birthDate'];
			$formattedDate = date('m-d-Y', strtotime($birthDate));
			echo h($formattedDate); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Hobby'); ?></dt>
		<dd>
			<?php echo h($user['User']['hobby']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User Since'); ?></dt>
		<dd>
			<?php $birthDate = $user['User']['createdAt'];
			$formattedDate = date('m-d-Y', strtotime($birthDate));
			echo h($formattedDate); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('LastLogin'); ?></dt>
		<dd>
			<?php echo h($user['User']['lastLogin']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $user['User']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
	</ul>
</div>

<style>
	.profile-pic {
		width: 50px;
		height: 50px;
		object-fit: cover;
		border: 2px solid #007bff;
		border-radius: 50%;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		transition: transform 0.2s;

	}
</style>