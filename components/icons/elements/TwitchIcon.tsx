import React from 'react';
import IconProps from '../IconProps'

const Icon: React.FC<IconProps> = ({ className, alt, color }) => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        className={className+' SocialLogin:Glyph'}
        aria-labelledby={alt}
        color={color || "currentColor"}
        fill={"none"}
    >
        <path fillRule="evenodd" clipRule="evenodd" d="M4 2.74998C3.80108 2.74998 3.61032 2.829 3.46966 2.96965C3.32901 3.11031 3.24999 3.30108 3.25 3.5L3.25035 16.5002C3.25035 16.6991 3.32938 16.8898 3.47004 17.0305C3.6107 17.1711 3.80147 17.2501 4.00039 17.2501L6.23309 17.25L6.25001 20.5039C6.25159 20.8068 6.43519 21.079 6.71542 21.1939C6.99565 21.3088 7.31751 21.2439 7.53129 21.0293L11.2968 17.2501L17.3049 17.25L20.75 13.811L20.75 3.49998C20.75 3.08576 20.4142 2.74998 20 2.74998L4 2.74998ZM11 7.49998L11 12.5H13V7.49998H11ZM15 7.49998V12.5H17V7.49998H15Z" fill="currentColor" />
    </svg>
);

export default Icon;