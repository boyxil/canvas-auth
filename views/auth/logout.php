<?php $auth = Auth::get_instance(); ?>
<h1><?php echo __('Logout'); ?></h1>


<ul>
	<li>
		<a href="<?php echo URL::site('welcome'); ?>"><?php echo __('Welcome'); ?></a>
	</li><li>
		<a href="<?php echo URL::site('auth/login'); ?>"><?php echo __('Login'); ?></a>
	</li>
</ul>

<?php if($auth->is_authenticated()): ?>
	<p><?php echo __('You are still logged in.'); ?></p>
<?php else: ?>
	<p><?php echo __('You have successfully logged out.'); ?></p>
<?php endif; ?>
