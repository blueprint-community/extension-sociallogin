import React, { useEffect, useState } from 'react';
import ContentBox from '@/components/elements/ContentBox';
import PageContentBlock from '@/components/elements/PageContentBlock';
import { LinkButton } from '@/components/elements/Button';
import http from '@/api/http';
import ProviderIcon from './icons/ProviderIcon';
import './style.css';

interface Provider {
  id: number;
  name: string;
  short_name: string;
  connection?: SocialConnection;
}

interface SocialConnection {
  id: number;
  name: string;
}

export default () => {
  const [providers, setProviders] = useState<Provider[]>([]);

  useEffect(() => {
    http.get('/extensions/sociallogin/connections').then((res) => {
      const data: Provider[] = res.data;
      setProviders(data);
    });
  }, []);

  return (
    <PageContentBlock title={'Social Setup'}>
      <div className={'mt-10 flex flex-wrap'}>
        {providers.length === 0 ? (
          <p>No available Social providers</p>
        ) : (
          providers.map((provider: Provider) => (
            <div className={'SocialLogin:EntryContainer w-full lg:w-1/4 md:w-1/2 inline-block p-4'}>
              <ContentBox key={provider.short_name} css={'mt-8 md:mt-0 inline-block w-full h-full'}>
                <div className={'SocialLogin:Entry w-full h-full'}>
                  {provider.connection ? (
                    <>
                      <ProviderIcon
                        name={provider.name}
                        short_name={provider.short_name}
                        color={'white'}
                        className={'w-8 float-right'}
                      />
                      <h3>{provider.name}</h3>

                      <div>
                        <div className={'mt-4 pb-14'}>
                          Connected to <span className={`font-bold`}>{provider.connection.name}</span>
                        </div>
                        <div className={'absolute bottom-4 left-4'}>
                          <LinkButton
                            color={'red'}
                            isSecondary
                            href={`/extensions/sociallogin/disconnect/${provider.short_name}`}
                          >
                            Disconnect
                          </LinkButton>
                        </div>
                      </div>
                    </>
                  ) : (
                    <>
                      <ProviderIcon
                        name={provider.name}
                        short_name={provider.short_name}
                        color={'white'}
                        className={'w-8 float-right opacity-25'}
                      />
                      <h3>{provider.name}</h3>

                      <div>
                        <div className={'mt-4 pb-14'}>Not connected to {provider.name}</div>
                        <div className={'absolute bottom-4 left-4'}>
                          <LinkButton
                            color={'green'}
                            isSecondary
                            href={`/extensions/sociallogin/connect/${provider.short_name}`}
                          >
                            Connect
                          </LinkButton>
                        </div>
                      </div>
                    </>
                  )}
                </div>
              </ContentBox>
            </div>
          ))
        )}
      </div>
    </PageContentBlock>
  );
};
