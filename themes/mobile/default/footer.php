<?php
if(Site::advLinks('bottom') !== false): ?>
    <div class="adv">
        <?=Site::advLinks('bottom')?>
    </div>
<? endif; ?>
<div class="footer txt-center">
    <span class="float-lt">&copy; <a href="<?=App::config('system/data/config')['copyright_link']?>"><?=App::config('system/data/config')['copyright']?></a></span>
    <?=_t('Online')?>: <?=user()->getOnline('users')+user()->getOnline('guests')?> (<a href="/users/online"><?=user()->getOnline('users')?></a> / <a href="/main/guests"><?=user()->getOnline('guests')?></a>)
    <span class="float-rt"><?=Site::icon('flags/'.Language::getLanguage())?> <a href="/main/language"><?=_t(Language::getLanguage(), 'languages')?></a></span>
</div>
<?php
if(Site::advLinks('counter') !== false): ?>
    <div class="img_counter">
        <?=Site::advLinks('counter')?>
    </div>
<? endif; ?>

</div>
</body>
</html>