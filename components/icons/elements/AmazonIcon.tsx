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
        <path fillRule="evenodd" clipRule="evenodd" d="M6 6.5L9.42133 6.9719C9.42133 6.9719 9.6779 4.82602 11.8704 4.82602C13.4704 4.82602 13.6664 6.36545 13.5 8C7 8 6 11.5 6 13C6 14.5 6.5 17 9.5 17C11.9 17 13.5 15.6667 14 15L15.5 16.5L18 14.5L17.4 13.5333C17.1404 13.1871 17 12.7661 17 12.3333V8.5C17 8.35628 17.0026 8.19966 17.0053 8.03272C17.0409 5.87651 17.1048 2 12 2C7.6 2 6.16667 5 6 6.5ZM13.5 10C13.6666 11.5 13.4 14.5 11 14.5C8.49997 14.5 8.99997 9.5 13.5 10Z" fill="currentColor" />
        <path d="M18.9883 18.323C11 22.8881 5.50003 18.3333 2.50003 16.5L1.99963 17.1578C1.99963 17.1578 6.50003 22 11 22C17 22 19.5 19 19.5 19L18.9883 18.323Z" fill="currentColor" />
        <path d="M20.6594 17.5058C19.6221 17.0253 17.8267 17.5058 17.8267 17.5058L17.5 16.9013C18.7267 15.8882 20.3364 15.9041 21.3465 16.1158C21.6857 16.1869 21.9456 16.4597 21.9817 16.8038C22.1457 18.3701 21.1741 19.7878 20.4669 20.5L19.9359 20.1011C19.9359 20.1011 20.6594 18.6792 20.6594 17.5058Z" fill="currentColor" />
    </svg>
);

export default Icon;