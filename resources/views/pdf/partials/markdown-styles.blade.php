{{-- Gaya markdown selaras dengan preview EasyMDE untuk DomPDF --}}
.md { font-family: "Times New Roman", Times, serif; font-size: 13px; line-height: 1.5; color: #000; }
.md > *:first-child { margin-top: 0; }
.md > *:last-child { margin-bottom: 0; }
.md p { margin: 0 0 8px 0; }
.md h1, .md h2, .md h3, .md h4, .md h5, .md h6 {
    margin: 10px 0 6px 0;
    font-weight: bold;
    line-height: 1.3;
}
.md h1 { font-size: 18px; }
.md h2 { font-size: 16px; }
.md h3 { font-size: 15px; }
.md h4, .md h5, .md h6 { font-size: 13px; }
.md strong, .md b { font-weight: bold; }
.md em, .md i { font-style: italic; }
.md ul, .md ol { margin: 0 0 8px 0; padding-left: 22px; }
.md li { margin: 0 0 4px 0; }
.md li > p { margin: 0 0 4px 0; }
.md blockquote {
    margin: 0 0 8px 0;
    padding: 4px 0 4px 12px;
    border-left: 3px solid #999;
    color: #333;
}
.md code {
    font-family: "Courier New", Courier, monospace;
    font-size: 12px;
    background: #f3f3f3;
    padding: 1px 4px;
}
.md pre {
    margin: 0 0 8px 0;
    padding: 8px 10px;
    background: #f3f3f3;
    white-space: pre-wrap;
    word-wrap: break-word;
}
.md pre code { background: transparent; padding: 0; }
.md a { color: #000; text-decoration: underline; }
.md hr { border: none; border-top: 1px solid #ccc; margin: 10px 0; }
.md table { width: 100%; border-collapse: collapse; margin: 0 0 8px 0; }
.md th, .md td { border: 1px solid #ccc; padding: 4px 6px; vertical-align: top; }
.md th { font-weight: bold; background: #f5f5f5; }
