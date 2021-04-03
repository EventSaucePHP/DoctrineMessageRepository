# Changelog

This changelog follows [the Keep a Changelog standard](https://keepachangelog.com).


## [Unreleased](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.8.3...master)


## [0.8.3 (2021-01-14)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.8.2...0.8.3)

### Changed
- Upgraded minimum PHP version ([#11](https://github.com/EventSaucePHP/DoctrineMessageRepository/pull/11))

### Fixed
- Type error related to Doctrine ([#10](https://github.com/EventSaucePHP/DoctrineMessageRepository/pull/10))


## [0.8.2 (2020-10-02)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.8.1...0.8.2)

### Fixed
- Retrieving additional events for a snapshot is now limited by the aggregate root ID ([#7](https://github.com/EventSaucePHP/DoctrineMessageRepository/pull/7))


## [0.8.1 (2020-05-01)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.8.0...0.8.1)

### Changed
- Update dependencies ([#6](https://github.com/EventSaucePHP/DoctrineMessageRepository/pull/6))


## [0.8.0 (2019-12-21)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.7.0...0.8.0)

### Changed
- Update to EventSauce 0.8.0 ([43f89f1](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/43f89f12dbe837539af33a4103e9b673c599a594))


## [0.7.0 (2019-10-04)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.6.0...0.7.0)

### Changed
- Update to EventSauce 0.7.0 ([3cda407](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/3cda407fb7abcf411957428456bab70cf5be9fc1))
- Always use a aggregate_root_id ([bca871f](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/bca871fd28a79f923a2c6084596f1bba5a3610cb))
- Require json extension ([6981868](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/6981868cc4c3f6a3ea7f3fae5cecb0513bc17d44))
- Added aggregate_root_version ([14495cd](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/14495cd8e3a878b5d3ab12c92cd3914d8ef182b7))
- Return last version ([569383a](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/569383ac66d37715a0b2efd5cd4215aa98f9ae98))

### Fixed
- Aggregate root ID should not be nullable ([a26f03e](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/a26f03efa03c889783e380082bcfe70dfdf79978))
- Fixed implementation ([c1aeec7](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/c1aeec72e39d0bd7189df8d689f8cf357aa8242a))


## [0.6.0 (2019-08-15)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.5.1...0.6.0)

### Changed
- Update to EventSauce 0.6.0 ([cdebd6a](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/cdebd6ab81278d8d94008f71cae496f8403621d7))


## [0.5.1 (2019-04-11)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.5.0...0.5.1)

### Fixed
- Allow json encode options to be configured ([2922a6f](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/2922a6f772923cc85c5b99663c4f49d4478b4db7))


## [0.5.0 (2019-01-06)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.4.0...0.5.0)

### Changed
- Update to EventSauce 0.5.0 ([d6abf73](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/d6abf73658b7c5fc73a615f59d44bb5ba54b1f22))


## [0.4.0 (2018-07-11)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.3.0...0.4.0)

### Changed
- Update to EventSauce 0.4.0 ([ad011ab](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/ad011ab1525e76627c8866d34957f81c53578340))
- Make time_of_recording more precise on postgres ([6256256](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/6256256f7a288a8e52e0331db4b04ae44fb56a7a))))
- Be better at mysql ([20628ef](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/20628ef65c0c069f82a77f152e852ed5f1790131))
- Use JSON field type ([2674812](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/2674812ef03d19c62babd38dafb09500a9a936e8))


## [0.3.0 (2018-03-23)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.2.0...0.3.0)

### Changed
- Update to EventSauce 0.3.0 ([1b7a5b6](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/1b7a5b6631eee751d43db0773f00ed560f1a017c))


## [0.2.0 (2018-03-13)](https://github.com/EventSaucePHP/DoctrineMessageRepository/compare/0.1.0...0.2.0)

### Changed
- Just use one class ([10e9c29](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/10e9c29ae809293910cf31ef55f94320a64119c3))
- Update to EventSauce 0.2.0 ([4b502de](https://github.com/EventSaucePHP/DoctrineMessageRepository/commit/4b502de6c9513f24566c2088b5c02c19f8591dab))


## 0.1.0 (2018-03-07)

Initial release.
