@if (\Auth::guest())
    <li>
        <a data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-user"></i> Sign In</a>
    </li>
@else
    @if (\Auth::user()->preferred_account_id !== null)
        <li>
            <a class="selected-player" href="<?= \Auth::user()->url(); ?>">
                <img src="<?= \Auth::user()->account->platformImage(); ?>" />
                <?= \Auth::user()->account->name; ?>
            </a>
        </li>
        <li>
            <a href="<?= route('logout'); ?>">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </li>
    @else
        <li>
            <a href="<?= route('switch'); ?>">
                <i class="fa fa-id-card" aria-hidden="true"></i> Select Account
            </a>
        </li>
    @endif
@endif