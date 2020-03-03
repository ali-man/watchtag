<?php
$dirname = "catalog";
if(rmdir($dirname))
{
  echo ("$dirname successfully removed");
}
else
{
  echo ("$dirname couldn't be removed"); 
}