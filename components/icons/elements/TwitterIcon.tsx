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
        <path d="M2 18.5C3.76504 19.521 5.81428 20 8 20C14.4808 20 19.7617 14.8625 19.9922 8.43797L22 4.5L18.6458 5C17.9407 4.37764 17.0144 4 16 4C13.4276 4 11.5007 6.51734 12.1209 8.98003C8.56784 9.20927 5.34867 7.0213 3.48693 4.10523C2.25147 8.30185 3.39629 13.3561 6.5 16.4705C6.5 16.4705 5 18.5 2 18.5Z" fill="currentColor" />
    </svg>
);

export default Icon;