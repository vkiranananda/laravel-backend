<?php

Backend::installResourceRoute('user', 'User');

Backend::installResourceRoute('user-role', 'User', 'Role');
Backend::installSortableRoute('sortable-user-role', 'User', 'Role');

