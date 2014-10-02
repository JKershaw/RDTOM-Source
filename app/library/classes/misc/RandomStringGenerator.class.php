<?php
class RandomStringGenerator
{
	public function generate($length) {
		$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$i = 0;
		$salt = "";
		do {
			$salt.= $characterList{mt_rand(0, strlen($characterList) - 1) };
			$i++;
		} while ($i < $length);
		return $salt;
	}
}
