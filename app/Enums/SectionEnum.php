<?php

namespace App\Enums;

enum SectionEnum: string
{
    const BG = 'bg_image';

    case HOME_BANNER = 'home_banner';
    case HOME_BANNERS = 'home_banners';

    case HOME_HOW_IT_WORK = 'home_how_it_work';
    case HOME_HOW_IT_WORKS = 'home_how_it_works';

    case HERO = 'hero';
    case HEROS = 'heros';

    //Footer
    case FOOTER = 'footer';
    case SOLUTION = "solution";

}
