import {themes as prismThemes} from 'prism-react-renderer';
import type {Config} from '@docusaurus/types';
import type * as Preset from '@docusaurus/preset-classic';

// This runs in Node.js - Don't use client-side code here (browser APIs, JSX...)

const config: Config = {
  title: 'Argus',
  favicon: 'img/logo.svg',
  url: 'https://your-docusaurus-site.example.com',
  // Set the /<baseUrl>/ pathname under which your site is served
  // For GitHub pages deployment, it is often '/<projectName>/'
  baseUrl: '/',
  organizationName: 'robiningelbrecht',
  projectName: 'argus',
  onBrokenLinks: "throw",
  onBrokenAnchors: "throw",
  onBrokenMarkdownLinks: "throw",
  onDuplicateRoutes: "throw",
  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      {
        docs: {
          sidebarPath: './sidebars.ts',
          breadcrumbs: true,
          routeBasePath: '/',
        },
        blog: false,
        theme: {
          customCss: './src/css/custom.css',
        },
      } satisfies Preset.Options,
    ],
  ],

  themeConfig: {

    navbar: {
      title: 'Argus',
      logo: {
      alt: 'Argus Logo',
        src: 'img/logo.svg',
      },
      items: [
        {
          href: 'https://github.com/robiningelbrecht/argus',
          label: 'GitHub',
          position: 'right',
        },
      ],
    },
    announcementBar: {
      content:
          'We are revamping our API docs. If you discover issues or have feedback, please <a target="_blank" rel="noopener noreferrer" href="https://github.com/robiningelbrecht/argus/issues/">contact us</a>!',
      backgroundColor: "#303846",
      textColor: "#ebedf0",
      isCloseable: false,
    },
    footer: {
      style: 'dark',
      copyright: `Copyright © ${new Date().getFullYear()} Argus, Inc. Built with Docusaurus.`,
    },
    prism: {
      theme: prismThemes.github,
      darkTheme: prismThemes.dracula,
    },
  } satisfies Preset.ThemeConfig,

  plugins: [
    [ require.resolve('docusaurus-lunr-search'), {
      excludeRoutes: [],
    }]
  ],
};

export default config;
