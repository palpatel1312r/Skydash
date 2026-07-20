<?php

if (!function_exists('setActiveMenu')) {
  function setActiveMenu($menuKey)
  {
    return session('active_menu') === $menuKey ? 'active' : '';
  }
}
