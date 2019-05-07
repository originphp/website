---
title: Debugging
description: Debugging Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Debugging

You can use `debug` function to debug variables and objects. You can also run a stack trace, by calling the `backtrace` function. Other utilities include `pr` which is similar to debug, but without the line numbers.

When errors are thrown in a web application, in debug mode, a full debug screen is shown where you can go through the whole stack trace.

In CLI, a pretty back trace is shown.

The `logs/application.log` file will show MySQL errors and any exceptions triggered in non-debug mode.