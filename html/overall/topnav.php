<header class="mbr-navbar mbr-navbar--freeze mbr-navbar--absolute mbr-navbar--sticky mbr-navbar--auto-collapse" id="ext_menu-0">
    <div class="mbr-navbar__section mbr-section">
        <div class="mbr-section__container container">
            <div class="mbr-navbar__container">
                <div class="mbr-navbar__column mbr-navbar__column--s mbr-navbar__brand">
                    <span class="mbr-navbar__brand-link mbr-brand mbr-brand--inline">
                        <span class="mbr-brand__logo"><a href="?view=index"><img class="mbr-navbar__brand-img mbr-brand__img" src="views/images/web/logo.png" alt="LOGO"></a></span>
                    </span>
                </div>
                <?php 
                if(!isset($_SESSION['app_id'])) {
                    echo "";
                } else {
                    if (strlen($_SESSION['app_id']['nombre']) + strlen($_SESSION['app_id']['apellido']) <= 30) {
                        $usuario = strtoupper($_SESSION['app_id']['nombre']) .' '. strtoupper($_SESSION['app_id']['apellido']);
                    } else {
                        $usuario = strtoupper($_SESSION['app_id']['nombre']);
                    }
                    
                    echo '
                <div class="mbr-navbar__hamburger mbr-hamburger text-white">
                    <span class="mbr-hamburger__line"></span>
                </div>
                <div class="mbr-navbar__column mbr-navbar__menu">
                    <nav class="mbr-navbar__menu-box mbr-navbar__menu-box--inline-right">
                        <div class="mbr-navbar__column">
                            <ul class="mbr-navbar__items mbr-navbar__items--right mbr-buttons mbr-buttons--freeze mbr-buttons--right btn-decorator mbr-buttons--active">
                                <li class="mbr-navbar__item">
                                    <a class="mbr-buttons__link btn text-white" href="#">'
                                        .$usuario .'
                                    </a>
                                </li>
                                <li class="mbr-navbar__item">
                                    <a class="mbr-buttons__link btn text-white" href="#">
                                        MIS DATOS
                                    </a>
                                </li>
                                <li class="mbr-navbar__item">
                                    <div class="mbr-navbar__column">
                                        <ul class="mbr-navbar__items mbr-navbar__items--right mbr-buttons mbr-buttons--freeze mbr-buttons--right btn-inverse mbr-buttons--active">
                                            <li class="mbr-navbar__item">
                                                <a class="mbr-buttons__btn btn btn-danger" href="?view=logout">
                                                    SALIR
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div> 
                    </nav>
                </div>';
                } ?> 
            </div>
        </div>
    </div>
</header>