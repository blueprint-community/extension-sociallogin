import React from 'react';

import GenericIcon from './elements/GenericIcon';
import AmazonIcon from './elements/AmazonIcon';
import AppleIcon from './elements/AppleIcon';
import DiscordIcon from './elements/DiscordIcon';
import DribbbleIcon from './elements/DribbbleIcon';
import DropboxIcon from './elements/DropboxIcon';
import FacebookIcon from './elements/FacebookIcon';
import FigmaIcon from './elements/FigmaIcon';
import GithubIcon from './elements/GithubIcon';
import GitlabIcon from './elements/GitlabIcon';
import GoogleIcon from './elements/GoogleIcon';
import InstagramIcon from './elements/InstagramIcon';
import LinkedinIcon from './elements/LinkedinIcon';
import MediumIcon from './elements/MediumIcon';
import MicrosoftIcon from './elements/MicrosoftIcon';
import ModrinthIcon from './elements/ModrinthIcon';
import OsuIcon from './elements/OsuIcon';
import PaymenterIcon from './elements/PaymenterIcon';
import PaypalIcon from './elements/PaypalIcon';
import PinterestIcon from './elements/PinterestIcon';
import RedditIcon from './elements/RedditIcon';
import SlackIcon from './elements/SlackIcon';
import SnapchatIcon from './elements/SnapchatIcon';
import SpotifyIcon from './elements/SpotifyIcon';
import SteamIcon from './elements/SteamIcon';
import TelegramIcon from './elements/TelegramIcon';
import TiktokIcon from './elements/TiktokIcon';
import TrelloIcon from './elements/TrelloIcon';
import TumblrIcon from './elements/TumblrIcon';
import TwitterIcon from './elements/TwitterIcon';
import TwitchIcon from './elements/TwitchIcon';
import UberIcon from './elements/UberIcon';
import UnsplashIcon from './elements/UnsplashIcon';
import VercelIcon from './elements/VercelIcon';
import WordpressIcon from './elements/WordpressIcon';

type IconComponentType = React.FC<{
    className?: string,
    alt?: string,
    color?: string
}>;

const IconMapping: Record<string, IconComponentType> = {
    generic: GenericIcon,
    amazon: AmazonIcon,
    apple: AppleIcon,
    discord: DiscordIcon,
    dribbble: DribbbleIcon,
    dropbox: DropboxIcon,
    facebook: FacebookIcon,
    figma: FigmaIcon,
    github: GithubIcon,
    gitlab: GitlabIcon,
    google: GoogleIcon,
    instagram: InstagramIcon,
    linkedin: LinkedinIcon,
    medium: MediumIcon,
    microsoft: MicrosoftIcon,
    modrinth: ModrinthIcon,
    osu: OsuIcon,
    paymenter: PaymenterIcon,
    paypal: PaypalIcon,
    pinterest: PinterestIcon,
    reddit: RedditIcon,
    slack: SlackIcon,
    snapchat: SnapchatIcon,
    spotify: SpotifyIcon,
    steam: SteamIcon,
    telegram: TelegramIcon,
    tiktok: TiktokIcon,
    trello: TrelloIcon,
    tumblr: TumblrIcon,
    twitter: TwitterIcon,
    twitch: TwitchIcon,
    uber: UberIcon,
    unsplash: UnsplashIcon,
    vercel: VercelIcon,
    wordpress: WordpressIcon,
};

export default IconMapping;
