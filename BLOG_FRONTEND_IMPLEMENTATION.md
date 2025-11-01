# Blog Frontend Implementation Guide

## Overview
The blog system now supports multiple images in multiple sections, similar to package details. Each blog can have:
- Multiple sections with custom titles
- Multiple images per section
- Ordered elements within sections
- Different element types: heading, subheading, paragraph, image, points, etc.

## Database Changes Required

**Run this migration:**
```sql
ALTER TABLE `blog_elements` 
ADD COLUMN `section_title` VARCHAR(255) NULL AFTER `element_type`,
ADD COLUMN `order` INT DEFAULT 0 AFTER `section_title`;
```

Or run the migration file: `database/migrations/2025_01_31_225000_add_order_and_section_to_blog_elements_table.php`

## API Response Structure

### Get Single Blog (GET /api/blogs/{id})

```json
{
  "id": 1,
  "title": "Best Time to Perform Umrah in 2025",
  "image": "/storage/blogs_images/main.jpg",
  "body": "Blog description...",
  "elements": [
    {
      "id": 1,
      "element_type": "heading",
      "value": "January - March - Perfect Weather",
      "section_title": "Month-by-Month Guide",
      "order": 0,
      "blog_id": 1
    },
    {
      "id": 2,
      "element_type": "image",
      "value": "/storage/blogs_images/jan-march.jpg",
      "section_title": "Month-by-Month Guide",
      "order": 1,
      "blog_id": 1
    },
    {
      "id": 3,
      "element_type": "paragraph",
      "value": "The three months of the year...",
      "section_title": "Month-by-Month Guide",
      "order": 2,
      "blog_id": 1
    }
  ],
  "elements_by_sections": {
    "Month-by-Month Guide": [
      {
        "id": 1,
        "element_type": "heading",
        "value": "January - March - Perfect Weather",
        "order": 0
      },
      {
        "id": 2,
        "element_type": "image",
        "value": "/storage/blogs_images/jan-march.jpg",
        "order": 1
      }
    ],
    "main": [
      {
        "id": 4,
        "element_type": "heading",
        "value": "Introduction",
        "order": 0
      }
    ]
  }
}
```

## Creating/Updating Blog

### Request Format (multipart/form-data)

**For Creating Blog (POST /api/blogs/create):**
```
title: "Best Time to Perform Umrah in 2025"
body: "Introduction text..."
image: [FILE]
elements: [
  JSON.stringify({
    element_type: "heading",
    value: "January - March - Perfect Weather",
    section_title: "Month-by-Month Guide",
    order: 0
  }),
  JSON.stringify({
    element_type: "image",
    value: "image_field_jan_march", // This will be the file field name
    section_title: "Month-by-Month Guide",
    order: 1
  }),
  JSON.stringify({
    element_type: "paragraph",
    value: "The three months of the year are one of the most appropriate periods...",
    section_title: "Month-by-Month Guide",
    order: 2
  }),
  JSON.stringify({
    element_type: "heading",
    value: "April - The Blessed Month of Ramadan",
    section_title: "Month-by-Month Guide",
    order: 3
  }),
  JSON.stringify({
    element_type: "image",
    value: "image_field_ramadan",
    section_title: "Month-by-Month Guide",
    order: 4
  })
]
image_field_jan_march: [FILE]
image_field_ramadan: [FILE]
```

**For Updating Blog (POST /api/blogs/{id} or PUT /api/blogs/{id}):**
- Same format as create
- For existing images, you can pass the URL directly in the value field
- For new images, use file field names

## Frontend Implementation

### 1. Blog Form Component Structure

```javascript
// Example React component structure
const BlogForm = () => {
  const [sections, setSections] = useState([
    {
      id: 'section-1',
      title: 'Month-by-Month Guide',
      elements: [
        {
          id: 'elem-1',
          type: 'heading',
          value: '',
          order: 0
        }
      ]
    }
  ]);

  const addSection = () => {
    // Add new section
  };

  const addElement = (sectionId, elementType) => {
    // Add new element to section
  };

  const addImage = (sectionId) => {
    // Add image element to section
  };

  const handleSubmit = async (formData) => {
    // Prepare FormData with sections and images
    const elements = [];
    let imageFieldIndex = 0;

    sections.forEach((section) => {
      section.elements.forEach((element, index) => {
        const elementData = {
          element_type: element.type,
          value: element.type === 'image' ? `image_field_${imageFieldIndex++}` : element.value,
          section_title: section.title,
          order: index
        };
        elements.push(JSON.stringify(elementData));

        // Add image file if element is image
        if (element.type === 'image' && element.file) {
          formData.append(`image_field_${imageFieldIndex - 1}`, element.file);
        }
      });
    });

    elements.forEach((elem, index) => {
      formData.append(`elements[${index}]`, elem);
    });

    // Submit to API
  };
};
```

### 2. Displaying Blog with Sections

```javascript
// Display component
const BlogDisplay = ({ blog }) => {
  const renderElement = (element) => {
    switch (element.element_type) {
      case 'heading':
        return <h2>{element.value}</h2>;
      case 'subheading':
        return <h3>{element.value}</h3>;
      case 'paragraph':
        return <p>{element.value}</p>;
      case 'image':
        return <img src={element.value} alt="" />;
      case 'points':
        const points = element.value.split(',').map(p => p.trim());
        return (
          <ul>
            {points.map((point, i) => <li key={i}>{point}</li>)}
          </ul>
        );
      default:
        return <div>{element.value}</div>;
    }
  };

  return (
    <div className="blog-content">
      <h1>{blog.title}</h1>
      <img src={blog.image} alt={blog.title} />
      
      {/* Display by sections */}
      {Object.entries(blog.elements_by_sections || {}).map(([sectionTitle, elements]) => (
        <div key={sectionTitle} className="blog-section">
          {sectionTitle !== 'main' && <h2 className="section-title">{sectionTitle}</h2>}
          {elements
            .sort((a, b) => a.order - b.order)
            .map((element) => (
              <div key={element.id} className="blog-element">
                {renderElement(element)}
              </div>
            ))}
        </div>
      ))}

      {/* Alternative: Display all elements in order */}
      {blog.elements
        .sort((a, b) => {
          // Sort by section_title, then by order
          if (a.section_title !== b.section_title) {
            return (a.section_title || '').localeCompare(b.section_title || '');
          }
          return a.order - b.order;
        })
        .map((element, index, array) => {
          const showSectionTitle = index === 0 || 
            array[index - 1].section_title !== element.section_title;
          
          return (
            <div key={element.id}>
              {showSectionTitle && element.section_title && 
                <h2 className="section-title">{element.section_title}</h2>
              }
              {renderElement(element)}
            </div>
          );
        })}
    </div>
  );
};
```

### 3. Section Management UI

```javascript
const SectionEditor = ({ section, onUpdate, onAddElement, onAddImage }) => {
  return (
    <div className="section-editor">
      <input
        type="text"
        value={section.title}
        onChange={(e) => onUpdate({ ...section, title: e.target.value })}
        placeholder="Section Title"
      />
      
      {section.elements.map((element, index) => (
        <div key={element.id} className="element-editor">
          <select
            value={element.type}
            onChange={(e) => {/* Update type */}}
          >
            <option value="heading">Heading</option>
            <option value="subheading">Subheading</option>
            <option value="paragraph">Paragraph</option>
            <option value="image">Image</option>
            <option value="points">Bullet Points</option>
          </select>
          
          {element.type === 'image' ? (
            <input
              type="file"
              onChange={(e) => {/* Handle image upload */}}
              accept="image/*"
            />
          ) : (
            <textarea
              value={element.value}
              onChange={(e) => {/* Update value */}}
              placeholder="Content..."
            />
          )}
          
          <button onClick={() => {/* Remove element */}}>Remove</button>
        </div>
      ))}
      
      <button onClick={onAddElement}>Add Text Element</button>
      <button onClick={onAddImage}>Add Image</button>
    </div>
  );
};
```

### 4. Complete Example Form

```javascript
const BlogEditor = () => {
  const [title, setTitle] = useState('');
  const [body, setBody] = useState('');
  const [mainImage, setMainImage] = useState(null);
  const [sections, setSections] = useState([]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData();
    
    formData.append('title', title);
    formData.append('body', body);
    if (mainImage) {
      formData.append('image', mainImage);
    }

    // Process sections into elements
    const elements = [];
    let imageCounter = 0;

    sections.forEach((section) => {
      section.elements.forEach((element) => {
        const elementData = {
          element_type: element.type,
          section_title: section.title || null,
          order: element.order || 0
        };

        if (element.type === 'image') {
          if (element.file) {
            // New image
            const fieldName = `image_field_${imageCounter}`;
            elementData.value = fieldName;
            formData.append(fieldName, element.file);
            imageCounter++;
          } else if (element.value) {
            // Existing image URL
            elementData.value = element.value;
          }
        } else {
          elementData.value = element.value || '';
        }

        elements.push(elementData);
      });
    });

    // Add elements as JSON strings
    elements.forEach((elem, index) => {
      formData.append(`elements[${index}]`, JSON.stringify(elem));
    });

    // Submit
    const response = await fetch('/api/blogs/create', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();
    console.log('Blog created:', result);
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="text"
        value={title}
        onChange={(e) => setTitle(e.target.value)}
        placeholder="Blog Title"
        required
      />
      
      <textarea
        value={body}
        onChange={(e) => setBody(e.target.value)}
        placeholder="Blog Description"
      />
      
      <input
        type="file"
        accept="image/*"
        onChange={(e) => setMainImage(e.target.files[0])}
        required
      />

      {/* Render sections */}
      {sections.map((section, sectionIndex) => (
        <SectionEditor
          key={section.id}
          section={section}
          onUpdate={(updated) => {
            const newSections = [...sections];
            newSections[sectionIndex] = updated;
            setSections(newSections);
          }}
        />
      ))}

      <button type="button" onClick={() => {
        setSections([...sections, {
          id: `section-${Date.now()}`,
          title: '',
          elements: []
        }]);
      }}>
        Add Section
      </button>

      <button type="submit">Save Blog</button>
    </form>
  );
};
```

## Element Types Supported

1. **heading** - Main heading (h2)
2. **subheading** - Subheading (h3)
3. **paragraph** - Paragraph text
4. **image** - Image with file upload support
5. **points** - Bullet points (comma-separated values)

## Key Points

1. **Sections**: Group related elements together with a `section_title`
2. **Ordering**: Use `order` field to control display order within sections
3. **Multiple Images**: Add multiple image elements in any section
4. **Image Handling**: 
   - New images: Use file field names (e.g., `image_field_0`)
   - Existing images: Pass URL directly in value field
5. **Backward Compatibility**: Elements without `section_title` will be grouped under "main" section

## Example Blog Structure for "Best Time to Perform Umrah 2025"

```javascript
const exampleBlog = {
  title: "Best Time to Perform Umrah in 2025: Month-by-Month Guide",
  body: "Performing Umrah is a very spiritual affair...",
  sections: [
    {
      title: "Month-by-Month Guide",
      elements: [
        { type: "heading", value: "January - March - Perfect Weather", order: 0 },
        { type: "image", value: "jan_march_image.jpg", order: 1 },
        { type: "paragraph", value: "The three months...", order: 2 },
        { type: "heading", value: "April - The Blessed Month of Ramadan", order: 3 },
        { type: "image", value: "ramadan_image.jpg", order: 4 },
        { type: "paragraph", value: "It is a very spiritual experience...", order: 5 }
      ]
    },
    {
      title: "Planning Your Umrah",
      elements: [
        { type: "heading", value: "Tips for Planning", order: 0 },
        { type: "points", value: "Book early, Consider weather, Choose right hotel", order: 1 },
        { type: "image", value: "planning_image.jpg", order: 2 }
      ]
    }
  ]
};
```

This structure allows for rich, multi-section blog posts with multiple images, similar to how package details are displayed.

