<?php
use App\Models\Member;
$f = \Faker\Factory::create('id_ID');
$members = Member::where('nis', '!=', '20260001')->get();
foreach($members as $m) {
    $name = $f->firstName() . ' ' . $f->lastName();
    $m->update(['nama' => $name]);
    $m->user->update(['name' => $name]);
}
echo 'DONE';
