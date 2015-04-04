# fastcat-manuals

```ruby
require 'redcarpet'
markdown = Redcarpet.new("Hello World!")
puts markdown.to_html
```

```java
public final TokenStream tokenStream(final String fieldName,
                                       final Reader reader, AnalyzerOption analyzerOption) throws IOException {
    TokenStreamComponents components = reuseStrategy.getReusableComponents(fieldName);
    
    final Reader r = initReader(fieldName, reader);
    if (components == null) {
      components = createComponents(fieldName, r);
      reuseStrategy.setReusableComponents(fieldName, components);
    } else {
      components.setReader(r);
    }
    components.setAnalyzerOption(analyzerOption);
    
    return components.getTokenStream();
  }
```