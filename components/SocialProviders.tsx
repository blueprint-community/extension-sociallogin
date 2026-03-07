import React, { useEffect, useState } from 'react';
import http from '@/api/http';
import { Button } from '@/components/elements/button/index';
import ProviderIcon from './icons/ProviderIcon';
import { useLocation } from 'react-router';
import MessageBox from '@/components/MessageBox';
import './style.css';

interface Provider {
  id: number;
  name: string;
  short_name: string;
}

export default () => {
  const [providers, setProviders] = useState<Provider[]>([]);
  useEffect(() => {
    http.get('/extensions/sociallogin/providers').then((res) => {
      const data: Provider[] = res.data;
      setProviders(data);
    });
  }, []);

  const [error, setError] = useState<string>('');
  const location = useLocation();
  useEffect(() => {
    const searchParams = new URLSearchParams(location.search);
    const message = searchParams.get('message');
    const provider = searchParams.get('provider');
    if (message == 'notconnected') {
      setError(`This ${provider} account is not connected to any account.`);
    } else {
      setError('');
    }
  }, [location]);

  return (
    <div className={'SocialLogin:container mt-4 flex flex-wrap justify-center bg-white shadow-lg rounded-lg p-6 mx-1'}>
      {!!error && <MessageBox type={'error'}>{error}</MessageBox>}
      {providers.length === 0 ? (
        <p>No available Social providers</p>
      ) : (
        providers.map((provider) => (
          <a
            tabIndex={-1}
            key={provider.short_name}
            href={`/extensions/sociallogin/redirect/${provider.short_name}`}
            className={`SocialLogin:provider ${providers.length === 1 ? 'w-full' : 'w-full md:w-1/2'}`}
          >
            <div className={'m-1'}>
              <Button.Text className={'w-full SocialLogin:providerButton SocialLogin:provider_' + provider.short_name}>
                <ProviderIcon
                  name={provider.name}
                  short_name={provider.short_name}
                  className={'SocialLogin:providerGlyph w-4 mr-2'}
                  color={'white'}
                />
                <span className={'SocialLogin:providerName'}>{provider.name}</span>
              </Button.Text>
            </div>
          </a>
        ))
      )}
    </div>
  );
};
