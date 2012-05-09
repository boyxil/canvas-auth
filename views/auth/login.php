<?php 

$auth = Auth::get_instance();
$userdata = $auth->get_user();

?>
<h1><?php echo __('Login'); ?></h1>

<ul>
	<li>
		<a href="<?php echo URL::site('welcome'); ?>"><?php echo __('Welcome'); ?></a>
	</li><li>
		<a href="<?php echo URL::site('auth/logout'); ?>"><?php echo __('Logout'); ?></a>
	</li>
</ul>

<?php if($auth->is_authenticated()): ?>
	<p><?php echo __('Hello ').$userdata['username']."."; ?></p>
<?php endif; ?>

<section>
	<?php echo Form::open(null, array('method' => 'post')); ?>
	<div>
		<?php echo Form::label('username', __('Username')); ?>
		<?php echo Form::input('username'); ?>
	</div>
	
	<div>
		<?php echo Form::label('password', __('Password')); ?>
		<?php echo Form::password('password'); ?>
	</div>
	
	<div>
		<?php echo Form::submit('login', 'Login'); ?>
	</div>
	<?php echo Form::close(); ?>
</section>
