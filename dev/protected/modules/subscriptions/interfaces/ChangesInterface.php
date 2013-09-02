<?php
interface ChangesInterface {
	function getChangesText ();

	function getChangesTitle();

	function getMtime();

	function getUrl();

	function getChangesIcon();
}