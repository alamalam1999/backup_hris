<?php

function gravatar($email,$size=40)
{
	//$default = "localhost/ticket/static/img/avatar.png";
	$avatar = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?d=mm&s=" . $size;
	if ( ! @getimagesize($avatar)) $avatar = base_url().'static/img/avatar.png';
	return $avatar;
}
